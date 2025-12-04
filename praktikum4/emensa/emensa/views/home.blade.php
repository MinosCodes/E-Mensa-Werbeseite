@extends('layouts.layout')

@section('cssextra')
    <link rel="stylesheet" href="/css/home.css">
@endsection

@section('content')
    <div class="col-12 home-wrapper">
        <header class="site-header" id="top">
            <div class="logo">E-Mensa Logo</div>
            <nav class="navBar" aria-label="Hauptnavigation">
                <a href="#ankuendigung">Ankündigung</a>
                <a href="#speisen">Speisen</a>
                <a href="#zahlen">Zahlen</a>
                <a href="#kontakt">Kontakt</a>
                <a href="#wichtig">Wichtig für uns</a>
            </nav>
        </header>

        <section id="ankuendigung" class="section Description">
            <h1>Bald gibt es Essen auch online :-)</h1>
            <p>
                Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
                Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet
                quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.
            </p>
        </section>

        <section id="speisen" class="section Speisen_Tabelle">
            <h1>Köstlichkeiten, die Sie erwarten</h1>
            <div class="sorting" role="group" aria-label="Sortieren nach Name">
                <span>Sortieren nach Name:</span>
                <a href="/?sort=ASC" class="{{ $sortDirection === 'ASC' ? 'active' : '' }}">Aufsteigend</a>
                <a href="/?sort=DESC" class="{{ $sortDirection === 'DESC' ? 'active' : '' }}">Absteigend</a>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Beschreibung</th>
                        <th>Erfasst am</th>
                        <th>Preis intern</th>
                        <th>Preis extern</th>
                        <th>Inhaltsstoffe</th>
                        <th>Foto</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($gerichte as $gericht)
                        <tr>
                            <td>{{ $gericht['name'] }}</td>
                            <td>{{ $gericht['beschreibung'] }}</td>
                            <td>{{ date('d.m.Y', strtotime($gericht['erfasst_am'])) }}</td>
                            <td>{{ number_format((float)$gericht['preisintern'], 2, ',', '.') }} €</td>
                            <td>{{ number_format((float)$gericht['preisextern'], 2, ',', '.') }} €</td>
                            <td>{{ $gericht['codes'] ?? '' }}</td>
                            <td>
                                <img src="/img/{{ $gericht['id'] }}.png" alt="Bild von {{ $gericht['name'] }}">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">Aktuell sind keine Gerichte verfügbar.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if(!empty($allergens))
                <ul class="allergen-list">
                    @foreach($allergens as $allergen)
                        <li><strong>{{ $allergen['code'] }}</strong> : {{ $allergen['name'] }}</li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section id="zahlen" class="section Zahlen">
            <h1>E-Mensa in Zahlen</h1>
            <div class="containerZahlen">
                <div>{{ $stats['visits'] ?? 0 }} Besuche</div>
                <div>{{ $stats['newsletter'] ?? 0 }} Anmeldungen zum Newsletter</div>
                <div>{{ $stats['dishes'] ?? 0 }} Speisen</div>
                <div class="wish-link-wrapper">
                    <a class="wish-link-button" href="/wunschgericht">Wunschgericht vorschlagen</a>
                </div>
            </div>
        </section>

        <section id="kontakt" class="section Container_Kontakt">
            <h1>Interesse geweckt? Wir informieren Sie</h1>

            @if($success)
                <p class="message-success">Vielen Dank, Ihre Anmeldung war erfolgreich!</p>
            @endif

            @if(!empty($errors))
                <div class="message-error" role="alert">
                    @foreach($errors as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="post" class="newsletter-form">
                <input type="hidden" name="csrf_token" value="{{ $csrfToken }}">

                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="{{ $formData['name'] }}" placeholder="Ihr Name">

                <label for="vorname">Vorname:</label>
                <input type="text" id="vorname" name="vorname" value="{{ $formData['vorname'] }}" placeholder="Ihr Vorname">

                <label for="email">E-Mail:</label>
                <input type="email" id="email" name="email" value="{{ $formData['email'] }}" placeholder="Ihre E-Mail Adresse">

                <label for="sprache">Sprache:</label>
                <select id="sprache" name="sprache">
                    <option value="deutsch" {{ $formData['sprache'] === 'deutsch' ? 'selected' : '' }}>Deutsch</option>
                    <option value="englisch" {{ $formData['sprache'] === 'englisch' ? 'selected' : '' }}>English</option>
                </select>

                <div id="datensch">
                    <label for="datenschutz">Datenschutz:</label>
                    <input type="checkbox" id="datenschutz" name="datenschutz" {{ $formData['datenschutz'] ? 'checked' : '' }}>
                    <span>Ich stimme den Datenschutzbedingungen zu</span>
                </div>

                <button type="submit">Zum Newsletter anmelden</button>
            </form>
        </section>

        <section id="wichtig" class="section">
            <h1>Das ist uns wichtig</h1>
            <ul>
                <li>Beste frische saisonale Zutaten</li>
                <li>Ausgewogene abwechslungsreiche Gerichte</li>
                <li>Sauberkeit</li>
            </ul>
            <h1>Wir freuen uns auf Ihren Besuch!</h1>
        </section>

        <footer id="footer" class="containerFooter">
            <hr>
            <div>(c) E-Mensa GmbH</div>
            <div>Adem / Amine</div>
            <div><a href="#top">Impressum</a></div>
        </footer>
    </div>
@endsection