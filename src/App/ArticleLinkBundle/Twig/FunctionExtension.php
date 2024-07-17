<?php
/**
 * This file is part of the AppArticleLinkBundle package.
 *
 */
namespace App\ArticleLinkBundle\Twig;

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
            new \Twig_SimpleFunction('articleLinksBox',[$this,'articleLinksBox'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('categoriesLinksBox',[$this,'categoriesLinksBox'],['is_safe' => ['html']]),
            ];

    }

    /**
     * Render search links box
     *
     * @return string
     */
    public function articleLinksBox(){
        $categories = $this->services->get('article_link_category.manager')->getAll();

        return $this->services->get('templating')
            ->render("AppArticleLinkBundle:Twig:articleLinksBox.html.twig",
                array("categories" => $categories));
    }

    /**
     * Render search links box
     *
     * @return string
     */
    public function categoriesLinksBox(){
        $categories = $this->services->get('articlecategory.manager')->getAllInBox();

        return $this->services->get('templating')
            ->render("AppArticleLinkBundle:Twig:categoriesLinksBox.html.twig",
                array("categories" => $categories));
    }

    /**
     * Get function name
     *
     * @return string
     */
    public function getName()
    {
        return 'article_link_function_extension';
    }
}

