<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Services\Block;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\AppBundle\Services\Block\BlockInterface;
use App\AppBundle\Entity\BlockElement;

/**
 * Class ArticleList
 *
 * @author wojciech przygoda
 */
class ArticleList implements BlockInterface {
    /**
     *
     * @var Container services container
     */
    private $services;
    /**
     *
     * @var array config parameters
     */
    private $config;
    /**
     *
     * @var BlockElement block element
     */
    private $element;

    /**
     * Constructor
     *
     * @param Container $container services container
     */
    function __construct(Container $container) {
      $this->services = $container;
    }
    /**
     * Set config
     *
     * @param array $config config parameters
     * @return ArticleList
     */
    public function setConfig(array $config) {
        $this->config = $config;
        return $this;
    }
    /**
     * Get config
     *
     * @return array
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Set block element
     *
     * @param BlockElement $element block element
     * @return ArticleList
     */
    public function setElement(BlockElement $element) {
        $this->element = $element;
        return $this;
    }

    /**
     * Get block elemet
     *
     * @return BlockElement
     */
    public function getElement() {
        return $this->element;
    }

    /**
     * Render article list
     *
     * @return string
     */
    public function render() {

        if(!isset($this->config['idCategory'])){
            return;
        }
        if(!isset($this->config['view'])){
            return;
        }
        $view = 'AppArticleBundle:Block:'.$this->config['view'].'.html.twig';

        return $this->services->get('templating')
                    ->render($view,
                    array('items'=>$this->services->get('article.manager')
                                        ->getPublishedByCategoryId($this->config['idCategory'],$this->config['limit']),
                          'category'=>$this->services->get('articlecategory.manager')
                                        ->findById($this->config['idCategory']),
                          'config'=>$this->config,
                          'element'=>$this->element));
    }

    /**
     * Get name
     * 
     * @return string
     */
    public function getName() {
        return 'Blok Lista Artykułów';
    }
}

