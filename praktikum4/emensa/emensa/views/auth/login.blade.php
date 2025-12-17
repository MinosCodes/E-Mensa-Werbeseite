@extends('layouts.layout')

@section('content')
    <div class="col-12">
        <h1>Anmeldung</h1>
        @if(!empty($error))
            <div style="color: #b00020; margin-bottom: 1rem;">{{ $error }}</div>
        @endif
        <form action="/anmeldung_verfizieren" method="post" style="max-width: 400px; display: grid; gap: 0.75rem;">
            <label for="email">E-Mail</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Passwort</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Anmeldung</button>
        </form>
    </div>
@endsection
