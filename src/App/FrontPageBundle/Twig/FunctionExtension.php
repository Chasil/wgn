<?php
/**
 * This file is part of the AppFrontPageBundle package.
 *
 */
namespace App\FrontPageBundle\Twig;

use \Twig_Filter_Function;
use \Twig_Filter_Method;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
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
            new \Twig_SimpleFunction('isMobile',[$this,'isMobile'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('getSettings',[$this,'getSettings'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('searchBox',[$this,'getSearchBox'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('avgPricem2',[$this,'avgPricem2']),
            ];

    }
    /**
     * Get average price for m2 similar offers
     * @return float
     */
    public function avgPricem2(Offer $offer){
        return $this->services->get('offer.manager')->getAvgPricem2($offer);
    }

    /**
     * Check if device is mobile
     * @return bool
     */
    public function isMobile(){
        return $this->services->get('detectdevice.manager')->DetectMobileLong();
    }

    /**
     * Get Settings
     *
     * @param string $key setting key
     * @return null|string
     */
    public function getSettings($key){
        $settings = $this->services->get('settings.manager')->get()->toArray();

        return (isset($settings[$key]))? $settings[$key]: null;
    }
    /**
     * Render Search box
     *
     * @return string
     */
    public function getSearchBox(){
        $searchManager = $this->services->get('search.manager');
         
         return $this->services->get('templating')
                     ->render("AppOfferBundle:Search:show.html.twig", array(
                         "form" => $searchManager->getFormView(),
                         "mobileForm" => $searchManager->getMobileFormView()
                     ));
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'front_page_function_extension';
    }
}

