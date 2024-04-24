<x-mail::message>
Bonjour,

Nous sommes dans l'obligation d'annuler votre inscription à l'expérience "{{ $booking->slot->manipulation->name }}".

Rappel de votre sélection :
 - Date : *{{ $booking->slot->start->translatedFormat('l d F Y')}}*
 - Heure : *{{ $booking->slot->start->translatedFormat('H\hi')}} à {{ $booking->slot->end->translatedFormat('H\hi')}}*

Si vous souhaitez vous réinscrire, vous pouvez cliquer sur le lien ci-dessous pour consulter la liste des créneaux restants pour cette expérience :

<x-mail::button :url="$targetUrl">
Voir les créneaux disponibles
</x-mail::button>

Avec toutes nos excuses,<br/>
Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
