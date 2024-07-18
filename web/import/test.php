<?php


define("zerogroszy", True);

function test() {
    echo slownie(1234567890.12).'<br />';
    echo slownie(0.01).'<br />';
    echo slownie(-1000).'<br />';
    // echo slownie(123456789012.12); // overflow
}

function ad($s1, $s2) /*(string $s1, string $s2)*/ {
  if (($s1 != '') && (s2 != '')) return $s1 . " " . $s2; else return $s1 . $s2;
}

function liczba($w, $s) /*(integer $w, string $s)*/ {
    $d1 = array("", "jeden", "dwa", "trzy", "cztery", "pięć", "sześć", "siedem", "osiem", "dziewięć");
    $d2 = array("", "dziesięć", "dwadzieścia", "trzydzieści", "czterdzieści", "pięćdziesiąt", "sześćdziesiąt", "siedemdziesiąt", "osiemdziesiąt", "dziewięćdziesiąt");
    $d3 = array("", "sto", "dwieście", "trzysta", "czterysta", "pięćset", "sześćset", "siedemset", "osiemset", "dziewięćset");
    $dx = array("dziesięć", "jedenaście", "dwanaście", "trzynaście", "czternaście", "piętnaście", "szesnaście", "siednaście", "osiemnście", "dziewiętnaście");

    $liczba = $d3[substr($s, 0, 1)];
    if (substr($s, 1, 1) == 1)
        $liczba = ad($liczba, $dx[substr($s, 2, 1)]);
    else
        $liczba = ad(ad($liczba, $d2[substr($s, 1, 1)]), $d1[substr($s, 2, 1)]);

    if (($w > 0) && ($liczba == $d1[1])) $liczba = "";
    return $liczba;
}

function slowo($w, $s) /*(integer $w, string $s)*/ {
    $w1 = array("tysiąc",  "tysiące",  "tysięcy");
    $w2 = array("milion",  "miliony",  "milionów");
    $w3 = array("miliard", "miliardy", "miliardów");
    $w4 = array("bilion",  "biliony",  "bilionów");
    $wz = array("złoty",   "złote",    "złotych");
    $wg = array("grosz",   "grosze",   "groszy");

    $j = $s; //IntVal($s);
    if ($j == 1) $n = 0; else $n = 2;
    $j = IntVal(substr($s, strlen($s) - 1, 1));
    $k = IntVal(substr($s, strlen($s) - 2, 1));
    if (($j > 1) && ($j < 5) && ($k <> 1)) $n = 1;
    if ($w ==  0) return ""; else
    if ($w ==  1) return $w1[$n]; else
    if ($w ==  2) return $w2[$n]; else
    if ($w ==  3) return $w3[$n]; else
    if ($w ==  4) return $w4[$n]; else
    if ($w == 20) return $wz[$n]; else
    if ($w == 21) return $wg[$n];
}

function slownie($x) {
    $m = $x < 0;
    if ($m) $x = -$x;
    $slownie = "";
    $p = IntVal($x);         // 123456789012
    $s = $p;                 // "123456789012" (123-456-789-012)

    while (strlen($s) % 3 != 0) $s = "0" . $s; // padding to whole triplets
    for ($w = 0; $w <= 4; $w++) {
        if (strlen($s) > $w * 3) {
            $c = substr($s, strlen($s) - $w * 3 - 2 - 1, 3); // i=0 => "012", i=1 => "789"
            if ($s != "000") // 2015-06 FIX
                $slownie = ad(ad(liczba($w, $c), slowo($w, $c)), $slownie);
        }
    }
    if ($slownie == "") $slownie = "zero";
    $slownie = ad($slownie, slowo(20, $s));

    $p = round(($x - $p) * 100);
    if ($p > 0) {
        $s = trim((string)$p);
        while (strlen($s) % 3 != 0) $s = "0" . $s;
        $slownie = ad($slownie, ad(liczba(0, $s), slowo(0, $s)));
        $slownie = ad($slownie, slowo(21, $s));
    } else {
         $slownie = $slownie . " zero groszy";
    }
    if ($m) $slownie = ad('minus', $slownie);
    return $slownie;
}

  test();
