@extends('examples.layout.m4_7d_layout')

@section('header')
    <h1>Layout-Demo Seite {{ $pageNumber }}</h1>
    <p>Dies ist die alternative Ansicht.</p>
@endsection

@section('main')
    <p>Die zweite Seite konzentriert sich auf Community-Events:</p>
    <ol>
        <li>Workshops zum nachhaltigen Kochen</li>
        <li>Meetups mit unseren Küchenchefs</li>
        <li>Live-Feedback der Mensa-Gäste</li>
    </ol>
@endsection

@section('footer')
    <small>&copy; {{ date('Y') }} E-Mensa – Seite 2</small>
@endsection
