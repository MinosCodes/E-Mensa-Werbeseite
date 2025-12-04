<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/../models/kategorie.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/../models/gericht.php');

class ExampleController
{
    public function m4_6a_queryparameter(RequestData $rd) {
        /*
           Wenn Sie hier landen:
           bearbeiten Sie diese Action,
           so dass Sie die Aufgabe löst
        */

        return view('notimplemented', [
            'request'=>$rd,
            'url' => 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"
        ]);
    }

    public function m4_7a_queryparameter(RequestData $rd): string
    {
        $rawName = trim($rd->getGetData()['name'] ?? '');
        $name = $rawName === '' ? 'nicht gesetzt' : $rawName;

        return view('examples.m4_7a_queryparameter', ['name' => $name]);
    }

    public function m4_7b_kategorie(): string
    {
        $categories = array_map(static fn($row) => $row['name'], db_kategorie_select_names_sorted());

        return view('examples.m4_7b_kategorie', ['categories' => $categories]);
    }

    public function m4_7c_gerichte(): string
    {
        $minPreis = 2.0;
        $gerichte = db_gericht_select_with_min_price($minPreis);

        return view('examples.m4_7c_gerichte', [
            'gerichte' => $gerichte,
            'minPreis' => $minPreis
        ]);
    }

    public function m4_7d_layout(RequestData $rd): string
    {
        $requested = (int)($rd->getGetData()['no'] ?? 1);
        $pageNumber = in_array($requested, [1, 2], true) ? $requested : 1;
        $viewName = $pageNumber === 2 ? 'examples.pages.m4_7d_page_2' : 'examples.pages.m4_7d_page_1';

        return view($viewName, [
            'title' => "Layout-Demo Seite {$pageNumber}",
            'pageNumber' => $pageNumber
        ]);
    }
}