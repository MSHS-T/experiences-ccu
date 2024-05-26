<x-mail::message>
Bonjour,

Nous vous adressons ce message suite à votre inscription à l'expérience "{{ $booking->slot->manipulation->name }}".

Rappel de votre sélection :
 - Date : *{{ $booking->slot->start->translatedFormat('l d F Y')}}*
 - Heure : *{{ $booking->slot->start->translatedFormat('H\hi')}} à {{ $booking->slot->end->translatedFormat('H\hi')}}*

Nous sommes au regret de constater que vous ne vous êtes pas présentés au rendez-vous prévu. Après analyse de votre historique, il apparaît que cette inscription est la troisième non honorée. En conséquence, votre adresse email a été bloquée sur notre plateforme, et il vous sera désormais impossible de vous inscrire à de futures expériences.

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
