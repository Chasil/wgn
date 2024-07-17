<?php
/**
 * This file is part of the AppMenuBundle package.
 *
 */
namespace App\MenuBundle\Twig;

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
            new \Twig_SimpleFunction('menu',[$this,'getMenu'],['is_safe' => ['html']]),
            ];

    }
    /**
     *
     * Get menu
     *
     * @param string $key menu key
     * @param array $params menu params
     * @return string
     */
    public function getMenu($key,$params=array()){
        $items = $this->services->get('menu.manager')->getByUniqueKey($key,true);

        return $this->services->get('templating')
                    ->render("AppMenuBundle:Menu:show.html.twig", array(
                                    "items" => $items,
                                    'params'=>$params));
    }
    /**
     * Get menu
     *
     * @return string
     */
    public function getName()
    {
        return 'menu_function_extension';
    }
}

