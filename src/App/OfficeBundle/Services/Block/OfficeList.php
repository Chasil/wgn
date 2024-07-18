<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Services\Block;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\AppBundle\Services\Block\BlockInterface;
use App\AppBundle\Entity\BlockElement;
use App\OfficeBundle\Entity\Office;
/**
 * Class OfficeList
 *
 * @author wojciech przygoda
 */
class OfficeList implements BlockInterface {
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

        $countryIsoCode = isset($this->config['countryIsoCode']) ? $this->config['countryIsoCode'] : 'pl';
        $offices = $this->services->get('office.manager')
                        ->getAll(Office::TYPE_PROPERTIES,1,$countryIsoCode);

        return $this->services->get('templating')
                    ->render("AppOfficeBundle:Block:list.html.twig",
                            array('offices' => $offices,
                                  'config'=>$this->config,
                                  'element'=>$this->element,));
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return 'Blok Lista Biur';
    }
}

