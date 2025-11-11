<?php
/**
 * Praktikum DBWT. Autoren:
 * Adem, Essouei, 3730582
 * Mohamed-amine, Merdassi, 3729412
 */

//replace
echo str_replace("not changed","changed","This was not changed"), "<br>";
//repeat
echo str_repeat("Hello ",5), "<br>";
//sub
echo substr("SubSection",3), "<br>";
//trim
$str = "Hello World!";
echo $str . "<br>";
//trim
echo trim($str,"Hed!"),"<br>";
//ltrim
echo ltrim($str,"Hello"),"<br>";
//rtrim
echo rtrim($str,"World!"),"<br>";
//String-Konkatenation
$first = "Hello";
$space = " ";
$second = "World!";
echo $first . $space . $second, "<br>";

