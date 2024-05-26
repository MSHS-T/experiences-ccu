<x-mail::message>
Bonjour,

Nous vous rappelons que vous êtes inscrits à l'expérience "{{ $booking->slot->manipulation->name }}".

Rappel de votre sélection :
 - Date : *{{ $booking->slot->start->translatedFormat('l d F Y')}}*
 - Heure : *{{ $booking->slot->start->translatedFormat('H\hi')}} à {{ $booking->slot->end->translatedFormat('H\hi')}}*

Vous devrez vous rendre à la plateforme CCU au rez-de-chaussée de la Maison de la Recherche de l'Université Toulouse-Jean-Jaurès (<a href="{{ asset(config('collabccu.access_map')) }}" target="_blank">{{ __('public.home.access_instructions') }}</a>).

Si votre venue n'est pas possible, veuillez cliquer sur le lien ci-dessous pour l'annuler :

<x-mail::button :url="$cancellationUrl">
Annuler mon inscription
</x-mail::button>

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
