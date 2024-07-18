<?php
/**
 * Created by PhpStorm.
 * User: Wojtek
 * Date: 16.05.2019
 * Time: 13:52
 */

namespace App\ArticleBundle\Twig;


class FilterExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('pageNumber', array($this, 'pageNumber'))
        );
    }
    public function pageNumber($text, $pageNumber){
        if($pageNumber < 1){
            return $text;
        }
        return str_replace('{page}','- strona ' . $pageNumber,$text);

    }
}