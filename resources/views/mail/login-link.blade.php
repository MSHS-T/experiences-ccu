<x-mail::message>
Bonjour,

Voici votre lien de connexion au site {{ config('app.name') }} :

<x-mail::button :url="$url">
Connexion
</x-mail::button>

Ce lien est valide durant une heure et n'est utilisable qu'une seule fois.

*Si vous n’avez pas demandé à recevoir cet e-mail, vous pouvez simplement l’ignorer.*

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
