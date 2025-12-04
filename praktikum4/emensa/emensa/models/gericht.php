<?php
/**
 * Diese Datei enthält alle SQL Statements für die Tabelle "gerichte"
 */
function db_gericht_select_all() {
    try {
        $link = connectdb();

        $sql = 'SELECT id, name, beschreibung FROM gericht ORDER BY name';
        $result = mysqli_query($link, $sql);

        $data = mysqli_fetch_all($result, MYSQLI_BOTH);

        mysqli_close($link);
    }
    catch (Exception $ex) {
        $data = array(
            'id'=>'-1',
            'error'=>true,
            'name' => 'Datenbankfehler '.$ex->getCode(),
            'beschreibung' => $ex->getMessage());
    }
    finally {
        return $data;
    }

}

function db_gericht_select_with_min_price(float $minPrice): array
{
    $link = connectdb();

    $minPrice = max($minPrice, 0.0);
    $sql = 'SELECT name, preisintern FROM gericht WHERE preisintern > ' . $minPrice . ' ORDER BY name DESC';
    $result = mysqli_query($link, $sql);

    $data = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

    mysqli_close($link);
    return $data ?: [];
}
