@extends('layouts.layout')

@section('content')
    <h1>Gerichte mit mehr als {{ number_format($minPreis, 2, ',', '.') }} &euro; internem Preis</h1>
    @if(empty($gerichte))
        <p>Es sind keine Gerichte vorhanden.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Gericht</th>
                    <th>Interner Preis</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gerichte as $gericht)
                    <tr>
                        <td>{{ $gericht['name'] }}</td>
                        <td>{{ number_format($gericht['preisintern'], 2, ',', '.') }} &euro;</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
