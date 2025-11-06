<?php
/**
 * - Praktikum DBWT. Autoren:
 * - Adem, Essouei, 3730582
 * - Mohamed-amine, Merdassi, 3729412
 */
echo str_replace("world","Lotfi","Hello world!");

echo str_repeat("HI",23)."<br>";

echo substr("Hello world",3)."<br>";



$str = "    Hello World!ltrim";
echo "Without ltrim: " . $str;
echo "<br>";
echo "With ltrim: " . ltrim($str);
echo "<br>";




$str = "Hello World!rtrim      ";
echo "Without rtrim: " . $str;
echo "<br>";
echo "With rtrim: " . rtrim($str);
echo "<br>";


$str = "                tHello World!rim          ";
echo "Without trim: " . $str;
echo "<br>";
echo "With trim: " . trim($str);
echo "<br>";


$str = "hey";
$str2 = "bro";
echo $str ." " . $str2 ." this"." is String"." ".'c'."oncatination";

