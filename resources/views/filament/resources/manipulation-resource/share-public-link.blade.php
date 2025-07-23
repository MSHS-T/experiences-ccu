@props(['record'])

@php($link = route('manipulation_slots', ['manipulation' => $record]))

<div x-data="{
    link: '{{ $link }}',
    copied: false,
    copyToClipboard() {
        // Try modern clipboard API first
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(this.link).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }).catch(() => {
                // Fallback if clipboard API fails
                this.fallbackCopyToClipboard();
            });
        } else {
            // Fallback for older browsers or non-HTTPS
            this.fallbackCopyToClipboard();
        }
        return false;
    },
    fallbackCopyToClipboard() {
        // Create a temporary input element
        const tempInput = document.createElement('input');
        tempInput.value = this.link;
        document.body.appendChild(tempInput);

        // Select and copy the text
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }
        } catch (err) {
            console.error('Failed to copy text: ', err);
        }

        // Clean up
        document.body.removeChild(tempInput);
    },
    downloadQRCode() {
        const svg = this.$refs.qrcode.querySelector('svg');
        if (!svg) return;

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const data = new XMLSerializer().serializeToString(svg);

        // Set canvas size to match SVG
        const svgRect = svg.getBoundingClientRect();
        canvas.width = 200;
        canvas.height = 200;

        const img = new Image();
        const blob = new Blob([data], { type: 'image/svg+xml' });
        const url = URL.createObjectURL(blob);

        img.onload = () => {
            ctx.drawImage(img, 0, 0, 200, 200);

            // Create download link
            const downloadUrl = canvas.toDataURL('image/png');
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.download = '{{ \Illuminate\Support\Str::slug($record->name) }}-qr-code.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);

            URL.revokeObjectURL(url);
        };

        img.src = url;
        return false;
    }
}">
    <div class="space-y-6">
        <!-- Link Display with Copy Button -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('messages.share_public_link.link') }}
            </label>
            <div class="flex items-center space-x-2">
                <input type="text" :value="link" readonly
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                <button @click.prevent="copyToClipboard()"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    :class="{ 'bg-green-600 hover:bg-green-700': copied }">
                    <span x-show="!copied">{{ __('messages.share_public_link.copy') }}</span>
                    <span x-show="copied">{{ __('messages.share_public_link.copied') }}</span>
                </button>
            </div>
        </div>

        <!-- QR Code Section -->
        <div class="space-y-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('messages.share_public_link.qrcode') }}
            </label>

            <div class="flex flex-col items-center space-y-4">
                <div x-ref="qrcode" class="p-4 bg-white rounded-lg border">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($link) !!}
                </div>

                <button @click.prevent="downloadQRCode()"
                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>{{ __('messages.share_public_link.download') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
