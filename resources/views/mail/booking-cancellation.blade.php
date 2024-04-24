<x-mail::message>
Bonjour,

Vous avez demandé l'annulation de votre inscription à l'expérience "{{ $booking->slot->manipulation->name }}".

Rappel de votre sélection :
 - Date : *{{ $booking->slot->start->translatedFormat('l d F Y')}}*
 - Heure : *{{ $booking->slot->start->translatedFormat('H\hi')}} à {{ $booking->slot->end->translatedFormat('H\hi')}}*

Veuillez cliquer sur le lien ci-dessous pour procéder à l'annulation de votre inscription :

<x-mail::button :url="$cancellationUrl">
Annuler mon inscription
</x-mail::button>

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
