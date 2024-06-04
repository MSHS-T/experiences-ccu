<x-mail::message>
Bonjour,

Vous avez reçu un message de contact sur la plateforme {{ config('app.name') }}.

Détails :
 - Nom : *{{ $name }}*
 - Email : *{{ $email }}*
 - Message : {{ $message }}

Vous pouvez répondre à l'utilisateur via la fonctionnalité "Répondre" de votre outil de messagerie.

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
