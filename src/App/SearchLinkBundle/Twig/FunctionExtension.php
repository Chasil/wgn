<?php
/**
 * This file is part of the AppSearchLinkBundle package.
 *
 */
namespace App\SearchLinkBundle\Twig;

use App\AppBundle\Component\SubdomainHelper;
use Symfony\Component\HttpFoundation\RequestStack;
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
            new \Twig_SimpleFunction('searchLinksBox',[$this,'searchLinksBox'],['is_safe' => ['html']]),
        ];

    }
    /**
     * Render search links box
     *
     * @return string
     */
     public function searchLinksBox($searchParams = null){
        $categories = $this->services->get('search_link_category.manager')->getAll();

        if(SubdomainHelper::isOfferSubdomain($this->services->get('request_stack')->getCurrentRequest()->get('subdomain'))){
            $params = SubdomainHelper::getParamsFromSubdomain($this->services->get('request_stack')->getCurrentRequest()->get('subdomain'));
            $transaction = $params[0];
            $city = $params[2];

            if($transaction == 'sprzedaz'){ $transaction="sprzedaż"; }

            return $this->services->get('templating')
                ->render("AppSearchLinkBundle:Twig:searchSubdomainLinksBox.html.twig",
                    array(
                        "categories" => $categories,
                        "city" => $city,
                        "transaction" => $transaction,
                        "searchParams" => $searchParams)
                );
        }


        return $this->services->get('templating')
                    ->render("AppSearchLinkBundle:Twig:searchLinksBox.html.twig",
                             array("categories" => $categories));
    }
//    public function searchLinksBox(){
//        $categories = $this->services->get('search_link_category.manager')->getAll();
//
//        return $this->services->get('templating')
//                    ->render("AppSearchLinkBundle:Twig:searchLinksBox.html.twig",
//                             array("categories" => $categories));
//    }
    /**
     * Get function name
     *
     * @return string
     */
    public function getName()
    {
        return 'search_link_function_extension';
    }
}

