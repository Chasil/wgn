<?php
/**
 * This file is part of the AppAppBundle package.
 *
 */
namespace App\AppBundle\Twig;

use Symfony\Component\HttpFoundation\Request;
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
            new \Twig_SimpleFunction('contentBloks',[$this,'getContentBloks'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('rating',[$this,'rating'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('voteCount',[$this,'voteCount'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('subdomainAlt',[$this,'subdomainAlt'],['is_safe' => ['html']]),
            ];
    }
    public function rating(){
        return $this->services->get('rating.manager')->getRating();
    }
    public function voteCount(){
        return $this->services->get('rating.manager')->getVoteCount();
    }
    /**
     *  Render all block elements
     *
     * @param string $place block elements place
     * @return type
     */
    public function getContentBloks($place){

        $output = '';
        $blockManager = $this->services->get('block.manager');
        $elements = $blockManager->getAllElements($place,true);

        if(!$elements){
            return;
        }

        foreach($elements as $element){
            $service = 'block.'.$element->getService();
            if($this->services->has($service)){
                $output .= $this->services->get($service)
                                ->setElement($element)
                                ->setConfig($element->getConfig())->render();
            }
        }

        return $output;
    }
    public function subdomainAlt($defaultAlt)
    {
        /**
         * @var Request $request
         */
        $request = $this->services->get('request');
        $routeName = $request->get('_route');

        if($routeName == 'app_archiv_homepage' || $routeName == 'frontend_offer_archive_subdomain')
        {
            return 'Archiwalne Oferty WGN – mieszkania i nieruchomości na sprzedaż';
        }

        $title = $this->services->get('subdomain.manager')->getSubdomainTitle();

        if(empty($title))
        {
            return $defaultAlt;
        }

        return $title;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'app_function_extension';
    }
}

