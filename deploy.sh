#!/bin/bash

# Abort on any failing command, unset variable, or broken pipe.
# Without this the script used to run every remaining step after a failure and
# still report success, which could bring the site back up with stale assets.
set -euo pipefail

# App name
APP_NAME="Expériences CCU"
LOCK_FILE="./deployment.lock"
BRANCH="develop"

# The Discord webhook URL is a secret and this repository is public, so it is
# never stored here. The caller supplies it with --webhook (or in the
# DISCORD_WEBHOOK_URL environment variable). It is optional: without it the
# deployment runs exactly the same, only without notifications.
DISCORD_WEBHOOK_URL="${DISCORD_WEBHOOK_URL:-}"
FORCE_DEPLOY=0

usage() {
    echo "Usage: $0 [--force] [--webhook <discord-webhook-url>]" >&2
}

while [ $# -gt 0 ]; do
    case "$1" in
        --force)
            FORCE_DEPLOY=1
            ;;
        --webhook)
            if [ $# -lt 2 ]; then
                echo "Error: --webhook requires a URL" >&2
                usage
                exit 1
            fi
            DISCORD_WEBHOOK_URL="$2"
            shift
            ;;
        --webhook=*)
            DISCORD_WEBHOOK_URL="${1#--webhook=}"
            ;;
        -h|--help)
            usage
            exit 0
            ;;
        *)
            echo "Error: unknown option: $1" >&2
            usage
            exit 1
            ;;
    esac
    shift
done

if [ -z "$DISCORD_WEBHOOK_URL" ]; then
    echo "Notice: no webhook URL given; deployment notifications are disabled." >&2
fi

# Find a matching CLI PHP binary
PHP_BIN=""
CGI_PHP_VERSION=$(/usr/bin/php -r 'echo PHP_VERSION;' 2>/dev/null || true)

# Search for matching CLI PHP binary
for path in /opt/cpanel/ea-php*/root/usr/bin/php /opt/alt/php*/usr/bin/php /usr/local/bin/php* /usr/bin/php*; do
    if [ -f "$path" ] && $path -r 'echo php_sapi_name();' 2>/dev/null | grep -q "cli"; then
        VERSION=$($path -r 'echo PHP_VERSION;' 2>/dev/null || true)
        if [ "$VERSION" = "$CGI_PHP_VERSION" ]; then
            PHP_BIN="$path"
            break
        fi
    fi
done

# Fallback to any PHP CLI binary
if [ -z "$PHP_BIN" ]; then
    for path in /opt/cpanel/ea-php*/root/usr/bin/php /opt/alt/php*/usr/bin/php /usr/local/bin/php* /usr/bin/php*; do
        if [ -f "$path" ] && $path -r 'echo php_sapi_name();' 2>/dev/null | grep -q "cli"; then
            PHP_BIN="$path"
            break
        fi
    done
fi

# Exit if no PHP CLI binary found
if [ -z "$PHP_BIN" ]; then
    echo "Error: Cannot find PHP CLI binary" >&2
    exit 1
fi

# Use full path to other executables
COMPOSER_BIN=$(command -v composer || true)
NPM_BIN=$(command -v npm || true)
GIT_BIN=$(command -v git || true)

for bin in COMPOSER_BIN NPM_BIN GIT_BIN; do
    if [ -z "${!bin}" ]; then
        echo "Error: Cannot find ${bin%_BIN} executable" >&2
        exit 1
    fi
done

# Send Discord notification
send_discord_notification() {
    # No webhook configured: skip silently rather than curl an empty URL.
    [ -n "${2:-}" ] || return 0
    curl -s -H "Content-Type: application/json" -X POST -d "{\"content\": \"$1\"}" "$2" > /dev/null || true
}

# Track the current step so a failure can name it
CURRENT_STEP="startup"
step() {
    CURRENT_STEP="$1"
    echo "==> $1"
}

# On any failure: keep the site in maintenance mode, keep the lock file, and
# report the failure. Never fall through to "artisan up" or a success message.
on_failure() {
    local exit_code=$?
    echo "Deployment FAILED during: ${CURRENT_STEP} (exit ${exit_code})" >&2
    send_discord_notification \
        "🔴 **$APP_NAME** deployment FAILED during \\\"${CURRENT_STEP}\\\" (exit ${exit_code}). Site is still in maintenance mode. Fix the cause, then re-run the deployment with \`--force\`." \
        "$DISCORD_WEBHOOK_URL"
    exit "$exit_code"
}

# Main deployment function
deploy() {
    trap on_failure ERR

    # Record start time
    start_time=$(date +%s)
    echo "$start_time" > "$LOCK_FILE"
    send_discord_notification "**$APP_NAME** deployment started" "$DISCORD_WEBHOOK_URL"

    # The site goes down before the code changes: pulling new PHP source while
    # the old vendor/ and cached config are still live would serve 500s.
    step "artisan down"
    $PHP_BIN artisan down --render="errors::503" --retry=15 --refresh=15

    step "git pull"
    $GIT_BIN checkout -- .
    $GIT_BIN pull

    step "composer install"
    $PHP_BIN $COMPOSER_BIN install --no-dev --optimize-autoloader

    # npm ci deletes node_modules before installing, so a failure here used to
    # leave no build tooling at all while the deploy carried on regardless.
    step "npm ci"
    $NPM_BIN ci

    step "npm run build"
    $NPM_BIN run build --silent

    # Refuse to continue if the build produced no manifest: @vite() would 500
    # on every page, or silently serve the previous release's assets.
    step "verify build output"
    if [ ! -s "public/build/manifest.json" ] && [ ! -s "public/build/.vite/manifest.json" ]; then
        echo "Error: no Vite manifest after build" >&2
        # `false`, not `exit`: an explicit exit would bypass the ERR trap and
        # skip the failure notification.
        false
    fi

    step "permissions"
    find . -type d -exec chmod 755 {} \; 2>/dev/null || true
    find . -type f -exec chmod 644 {} \; 2>/dev/null || true
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true
    # The sweep above clears the executable bit on this script itself
    chmod 755 deploy.sh 2>/dev/null || true

    step "cache rebuild"
    $PHP_BIN artisan optimize:clear
    $PHP_BIN artisan config:cache
    # $PHP_BIN artisan icons:cache
    $PHP_BIN artisan route:cache
    $PHP_BIN artisan view:cache

    step "migrate"
    $PHP_BIN artisan migrate --force

    step "seed roles and permissions"
    $PHP_BIN artisan db:seed --class="RolesPermissionsSeeder" --force

    step "artisan up"
    $PHP_BIN artisan up
    # $PHP_BIN artisan sitemap:generate

    # Clean up and notify — only reached when every step above succeeded
    trap - ERR
    rm "$LOCK_FILE"
    end_time=$(date +%s)
    duration=$((end_time - start_time))
    send_discord_notification "**$APP_NAME** deployment finished (duration: ${duration} seconds)" "$DISCORD_WEBHOOK_URL"
}

# Check if we should deploy
if [ "$FORCE_DEPLOY" -eq 1 ] || { $GIT_BIN fetch -q origin && [ -n "$($GIT_BIN log --oneline ..origin/$BRANCH)" ]; }; then
    deploy
fi

exit 0
