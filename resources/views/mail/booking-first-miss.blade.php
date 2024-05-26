<x-mail::message>
Bonjour,

Nous vous adressons ce message suite à votre inscription à l'expérience "{{ $booking->slot->manipulation->name }}".

Rappel de votre sélection :
 - Date : *{{ $booking->slot->start->translatedFormat('l d F Y')}}*
 - Heure : *{{ $booking->slot->start->translatedFormat('H\hi')}} à {{ $booking->slot->end->translatedFormat('H\hi')}}*

Nous sommes au regret de constater que vous ne vous êtes pas présentés au rendez-vous prévu, et vous informons qu'au bout de la troisième inscription non honorée, votre adresse e-mail sera automatiquement bloquée sur notre plateforme.

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
