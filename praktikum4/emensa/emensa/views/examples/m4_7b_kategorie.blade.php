@extends('layouts.layout')

@section('cssextra')
    <style>
        .category-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .category-list li {
            padding: 0.35rem 0.5rem;
            border-bottom: 1px solid #ddd;
        }
        .category-list li:nth-child(even) {
            font-weight: 700;
        }
    </style>
@endsection

@section('content')
    <h1>Kategorien (aufsteigend)</h1>
    @if(empty($categories))
        <p>Es wurden keine Kategorien gefunden.</p>
    @else
        <ul class="category-list">
            @foreach($categories as $category)
                <li>{{ $category }}</li>
            @endforeach
        </ul>
    @endif
@endsection
