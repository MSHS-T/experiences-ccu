<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    @include('export._style')
</head>

<body>
    <div id="logo-wrapper">
        <img src="{{ base_path() }}/public/favicon.png" alt="{{ config('app.name') }}" id="logo" width="32">
        {{ config('app.name') }}
    </div>
    <div class="title-bordered">
        <h2 class="text-center"><u>Manipulation :</u> {{ $manipulation->name }}</h2>
        <h3 class="text-center">Feuille d'appel du {{ (new \Carbon\Carbon($date))->format('d/m/Y') }}</h3>
    </div>
    <br />
    <table>
        <thead>
            <tr>
                <th scope="col">Heure de début</th>
                <th scope="col">Heure de Fin</th>
                <th scope="col">Nom</th>
                <th scope="col">Prénom</th>
                <th scope="col">Email</th>
                <th scope="col">Confirmé ?</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($slots as $index => $slot)
            <tr class="row-{{ $index % 2 === 0 ? 'even' : 'odd' }}">
                <td class="text-center">{{ $slot->start->format('H:i') }}</td>
                <td class="text-center">{{ $slot->end->format('H:i') }}</td>
                @if($slot->booking !== null)
                <td class="text-center">{{ strtoupper($slot->booking->last_name) }}</td>
                <td class="text-center">{{ $slot->booking->first_name }}</td>
                <td class="text-center">{{ $slot->booking->email }}</td>
                <td class="text-center">{{ $slot->booking->confirmed ? 'Oui' : 'Non' }}</td>
                @else
                <td class="text-center" colspan="4">Pas de réservation</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>