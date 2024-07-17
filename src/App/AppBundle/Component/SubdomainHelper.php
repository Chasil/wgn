<?php
/**
 * Created by PhpStorm.
 * User: Wojtek
 * Date: 28.11.2018
 * Time: 17:01
 */

namespace App\AppBundle\Component;


use App\ArticleBundle\Entity\Blog;
use App\OfferBundle\Entity\Category;

class SubdomainHelper
{
    public static function getH1($subdomain)
    {

    }
    public static function prepareSubdomain(Blog $blog)
    {
        $categories = self::categoriesDictionary();

        $transactionTypes = self::transactionsDictionary();
        $subdomain = $transactionTypes[$blog->getTransactionType()->getId()]['key']
            . '-' . $categories[$blog->getCategory()->getId()]['key'];

        if(!empty($blog->getCity())) {
            $subdomain .= '-' . $blog->getCity();
        }
        return $subdomain;

    }
    public static function transactionsDictionary()
    {
        return [
            1=>[
                'key'=>'sprzedaz',
                'name'=>'Sprzedaż',
                'id'=>1
            ],
            2=>[
                'key'=>'kupno',
                'name'=>'Kupno',
                'id'=>2
            ],
            3=>[
                'key'=>'wynajem',
                'name'=>'Wynajem',
                'id'=>3
            ],
            4=>[
                'key'=>'najem',
                'name'=>'Najem',
                'id'=>4
            ],
        ];
    }
    public static function categoriesDictionary()
    {
        return [
            1 => [
                'key'=>'mieszkanie',
                'original_key'=>Category::TYPE_FLAT,
                'name'=>'Mieszkanie',
                'plural'=>'Mieszkania',
                'second_plural'=>'Mieszkań',
                'id'=>1,
            ],
            2 =>  [
                'key'=>'dom',
                'original_key'=>Category::TYPE_HOUSE,
                'name'=>'Dom',
                'plural'=>'Domy',
                'second_plural'=>'Domów',
                'id'=>2,
            ],
            3 =>[
                'key'=>'dzialka',
                'original_key'=>Category::TYPE_PLOT,
                'name'=>'Działka',
                'plural'=>'Działki',
                'second_plural'=>'Działek',
                'id'=>3,
            ],
            4 => [
                'key'=>'lokal',
                'original_key'=>Category::TYPE_LOCAL,
                'name'=>'Lokal',
                'plural'=>'Lokale',
                'second_plural'=>'Lokali',
                'id'=>4,
            ],
            5 => [
                'key'=>'komercyjne',
                'original_key'=>Category::TYPE_COMMERCIAL,
                'name'=>'Nieruchomość Komercyjna',
                'plural'=>'Nieruchomości Komercyjne',
                'second_plural'=>'Nieruchomości Komercyjnych',
                'id'=>5,
            ],
            6 =>[
                'key'=>'garaz',
                'original_key'=>Category::TYPE_GARAGE,
                'name'=>'Garaż',
                'plural'=>'Garaże',
                'second_plural'=>'Garaży',
                'id'=>6,
            ],
        ];
    }
    public static function getTransactionByKey($key)
    {
        $transactions = self::transactionsDictionary();

        foreach($transactions as $transaction)
        {
            if($transaction['key'] == $key)
            {
                return $transaction;
            }
        }

        return null;
    }
    public static function getTransactionById($id)
    {
        $transactions = self::transactionsDictionary();

        foreach($transactions as $transaction)
        {
            if($transaction['id'] == $id)
            {
                return $transaction;
            }
        }

        return null;
    }
    public static function getCategoryByKey($key)
    {
        $categories = self::categoriesDictionary();
        foreach($categories as $category)
        {
            if($category['key'] == $key)
            {
                return $category;
            }
        }
        return null;

    }
    public static function getCategoryByOriginalKey($key)
    {
        $categories = self::categoriesDictionary();
        foreach($categories as $category)
        {
            if($category['original_key'] == $key)
            {
                return $category;
            }
        }
        return null;

    }
    public static function getCategoryById($id)
    {
        $categories = self::categoriesDictionary();
        foreach($categories as $category)
        {
            if($category['id'] == $id)
            {
                return $category;
            }
        }

        return null;

    }
    public static function getTransactionId($key)
    {
        $transactionTypes = self::transactionsDictionary();

        return array_search($key, array_column($transactionTypes, 'key'));
    }
    public static function getCategoryId($key)
    {
        $categoryTypes = self::categoriesDictionary();

        return array_search($key, array_column($categoryTypes, 'key'));
    }
    public static function getParamsFromSubdomain($subdomain)
    {
        $parts = explode('-', $subdomain);

        if(count($parts) > 3)
        {
            $newParts = [$parts[0],$parts[1]];
            unset($parts[0]);
            unset($parts[1]);
            $newParts[] = join('-',$parts);


            return $newParts;
        }

        return $parts;
    }
    public static function prepareSubdomainFromParameters($categoryId, $transactionId, $locationKey)
    {
        $categories = self::categoriesDictionary();

        $transactionTypes = self::transactionsDictionary();
        $subdomain = $transactionTypes[$transactionId]['key']
            . '-' . $categories[$categoryId]['key'];

        if(!empty($locationKey)) {
            $subdomain .= '-' . $locationKey;
        }
        return $subdomain;

    }

    public static function isOfferSubdomain($subdomain)
    {
        if(is_null($subdomain))
        {
            return false;
        }

        return (bool) preg_match('/^(([[:alnum:]]*-[[:alnum:]]*-[[:alnum:]-]*)*)$/', $subdomain);
    }
}
