<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Twig;

use \Twig_Filter_Function;
use \Twig_Filter_Method;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\AdBundle\Entity\AdPosition;

/**
 * Class FunctionExtension
 *
 * @author wojciech przygoda
 */
class FunctionExtension extends \Twig_Extension
{
    /**
     *
     * @var Container services container
     */
    private $services;

    /**
     * Constructor
     *
     * @param Container $container services container
     */
    function __construct(Container $container) {

        $this->services = $container;
    }
    /**
     * Return the functions registered as twig extensions
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('file_exists',[$this,'file_exists'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('adsBox',[$this,'getAdsBox'],['is_safe' => ['html']]),
            ];
    }
    public function fileExists($filename){
        return file_exists($filename);
    }
    /**
     * Get ads box
     *
     * @param string $positionKey ad position key
     * @return null|string
     */
    public function getAdsBox($positionKey){
        $position = $this->services->get('adposition.manager')->findByKey($positionKey);
        if(!is_object($position)){
            return;
        }
        $total = $position->getAdsLimit();
        $ads = $this->services->get('ad.manager')
                    ->getAllPublishByPosition($position->getId());

        $rows = count($ads);
        if($rows ==0){
            return;
        }
        while ($total > $rows){
           $total--;
        }

       if ($position->getMode()==AdPosition::MODE_RANDOM) {
            shuffle($ads);
            $indexes = array_rand($ads);
            $itemsCount = count($indexes);
            for ($i = 0; $i < $itemsCount; $i++) {
                if($itemsCount > 1){
                    $index = $indexes[$i];
                }else {
                    $index = 0;
                }
                $selectedAds[] = $ads[$index];
            }
       } else {
            for ($i = 0; $i <= $total - 1; $i++){
               $selectedAds[] = $ads[$i];
            }
       }

        foreach ($selectedAds as $ad) {
            $this->services->get('ad.manager')->incrementHits($ad['id']);
        }

        return $this->services->get('templating')
                    ->render("AppAdBundle:Ad:adsBox.html.twig", array("ads" => $selectedAds));
    }
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'ad_function_extension';
    }
}

