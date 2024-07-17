<?php
namespace App\AppBundle\Component;
/**
 * Created by PhpStorm.
 * User: Wojtek
 * Date: 26.11.2018
 * Time: 19:33
 */

class UrlHelper
{
    public static function prepare($url, $replace=array(), $delimiter='-')
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        if( !empty($replace) )
        {
            $url = str_replace((array)$replace, ' ', $url);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $url);
        $clean = preg_replace("/[^a-zA-Z0-9_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }
}