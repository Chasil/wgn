<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Twig;

use \Twig_Filter_Function;
use \Twig_Filter_Method;
use App\PaymentBundle\Entity\Payment;

/**
 * Class FunctionExtension
 *
 * @author wojciech przygoda
 */
class FunctionExtension extends \Twig_Extension
{
    /**
     * Return the functions registered as twig extensions
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('paymentName',[$this,'getPaymentName'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('paymentStateName',[$this,'getPaymentStateName'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('amountInWords',[$this,'amountInWords'],['is_safe' => ['html']]),
            ];
    }
    /**
     * Get payment name
     *
     * @param string $paymentMethod payment method
     * @return string
     */
    public function getPaymentName($paymentMethod) {

        switch($paymentMethod){
            case Payment::TYPE_CARD:
                $name = 'Płatność elektroniczna';
            break;
            case Payment::TYPE_SMS:
                $name = 'SMS';
            break;
            case Payment::TYPE_SUBSCRIPTION:
                $name = 'Abonament';
            break;
            default:
                $name = '';
            break;
        }
        return $name;
    }
    /**
     * Get Payment state
     *
     * @param int $paymentState
     * @return string
     */
    public function getPaymentStateName($paymentState) {

        switch($paymentState){
            case Payment::STATE_FAILD:
                $name = 'Odrzucona';
            break;
            case Payment::STATE_STARTED:
                $name = 'Rozpoczęta';
            break;
            case Payment::STATE_SUCCESS:
                $name = 'Zakończona';
            break;
            default:
                $name = '';
            break;
        }
        return $name;
    }
    /**
     * Ad
     * @param string $s1 s1
     * @param string $s2 s2
     * @return string
     */
    function ad($s1, $s2) /*(string $s1, string $s2)*/ {
      if (($s1 != '') && ($s2 != '')) return $s1 . " " . $s2; else return $s1 . $s2;
    }
    /**
     * Transform numbers to words
     * @param int $w w
     * @param int $s s
     * @return string
     */
    function liczba($w, $s) {
        $d1 = array("", "jeden", "dwa", "trzy", "cztery", "pięć", "sześć", "siedem", "osiem", "dziewięć");
        $d2 = array("", "dziesięć", "dwadzieścia", "trzydzieści", "czterdzieści", "pięćdziesiąt", "sześćdziesiąt", "siedemdziesiąt", "osiemdziesiąt", "dziewięćdziesiąt");
        $d3 = array("", "sto", "dwieście", "trzysta", "czterysta", "pięćset", "sześćset", "siedemset", "osiemset", "dziewięćset");
        $dx = array("dziesięć", "jedenaście", "dwanaście", "trzynaście", "czternaście", "piętnaście", "szesnaście", "siednaście", "osiemnście", "dziewiętnaście");

        $liczba = $d3[substr($s, 0, 1)];
        if (substr($s, 1, 1) == 1)
            $liczba = $this->ad($liczba, $dx[substr($s, 2, 1)]);
        else
            $liczba = $this->ad($this->ad($liczba, $d2[substr($s, 1, 1)]), $d1[substr($s, 2, 1)]);

        if (($w > 0) && ($liczba == $d1[1])) $liczba = "";
        return $liczba;
    }
    /**
     * Add words
     *
     * @param int $w w
     * @param int $s s
     * @return string
     */
    function slowo($w, $s) /*(integer $w, string $s)*/ {
        $w1 = array("tysiąc",  "tysiące",  "tysięcy");
        $w2 = array("milion",  "miliony",  "milionów");
        $w3 = array("miliard", "miliardy", "miliardów");
        $w4 = array("bilion",  "biliony",  "bilionów");
        $wz = array("złoty",   "złote",    "złotych");
        //$wg = array("gr",   "gr",   "gr");

        $j = $s; //IntVal($s);
        if ($j == 1) $n = 0; else $n = 2;
        $j = IntVal(substr($s, strlen($s) - 1, 1));
        $k = IntVal(substr($s, strlen($s) - 2, 1));
        if (($j > 1) && ($j < 5) && ($k <> 1)) $n = 1;
        if ($w ==  0) return ""; else
        if ($w ==  1) return $w1[$n]; else
        if ($w ==  2) return $w2[$n]; else
        if ($w ==  3) return $w3[$n]; else
        if ($w ==  4) return $w4[$n];
    }
    /**
     * Amount in words
     *
     * @param int $x x
     * @return string
     */
    function amountInWords($x) {
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
                    $slownie = $this->ad($this->ad($this->liczba($w, $c), $this->slowo($w, $c)), $slownie);
            }
        }
        if ($slownie == "") $slownie = "zero";
        $slownie = $this->ad($slownie, $this->slowo(20, $s));

        $p = round(($x - $p) * 100);
        $slownie .= ' i 0,';
        if ($p > 0) {
            $slownie .= trim((string)$p);
        } else {
             $slownie .= "00";
        }
        $slownie .= " PLN";
        if ($m) $slownie = $this->ad('minus', $slownie);
        return $slownie;
    }
    /**
     * Get function name
     *
     * @return string
     */
    public function getName()
    {
        return 'payment_function_extension';
    }
}

