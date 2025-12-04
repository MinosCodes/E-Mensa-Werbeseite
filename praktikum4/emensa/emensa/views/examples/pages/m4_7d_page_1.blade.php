@extends('examples.layout.m4_7d_layout')

@section('header')
    <h1>Layout-Demo Seite {{ $pageNumber }}</h1>
    <p>Willkommen bei der ersten Variante.</p>
@endsection

@section('main')
    <p>Diese Seite hebt die saisonalen Highlights der E-Mensa hervor.</p>
    <ul>
        <li>Knackige Bowls mit lokalen Zutaten</li>
        <li>Frische Pasta mit hausgemachten Soßen</li>
        <li>Leichte Desserts für den Nachmittag</li>
    </ul>
@endsection

@section('footer')
    <small>&copy; {{ date('Y') }} E-Mensa – Seite 1</small>
@endsection
