<x-mail::message>
Bonjour,

Nous sommes dans l'obligation d'annuler votre inscription à l'expérience "{{ $manipulation }}".

Rappel de votre sélection :
 - Date : *{{ $start->translatedFormat('l d F Y')}}*
 - Heure : *{{ $start->translatedFormat('H\hi')}} à {{ $end->translatedFormat('H\hi')}}*

Si vous souhaitez vous réinscrire, vous pouvez cliquer sur le lien ci-dessous pour consulter la liste des créneaux restants pour cette expérience :

<x-mail::button :url="$targetUrl">
Voir les créneaux disponibles
</x-mail::button>

Avec toutes nos excuses,<br/>
Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
