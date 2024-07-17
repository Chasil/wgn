<?php
/**
 * This file is part of the AppSettingsBundle package.
 *
 */
namespace App\SettingsBundle\Services\Block;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\AppBundle\Services\Block\BlockInterface;
use App\AppBundle\Entity\BlockElement;

/**
 * Class CompanyData
 *
 * @author wojciech przygoda
 */
class CompanyData implements BlockInterface {
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
     * Render company data
     *
     * @return string
     */
    public function render() {


        $view = 'AppSettingsBundle:Block:companyData.html.twig';

        return $this->services->get('templating')
                    ->render($view,
                    array('settings'=>$this->services->get('settings.manager')
                                        ->get(),
                          'config'=>$this->config,
                          'element'=>$this->element));
    }
    /**
     * Get name
     *
     * @return string
     */
    public function getName(){
        return 'Dane Firmy';
    }
}
