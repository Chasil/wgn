<?php

namespace App\OfferBundle\Services\Block;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\AppBundle\Services\Block\BlockInterface;
use App\AppBundle\Entity\BlockElement;


class ArchivedOffers implements BlockInterface
{
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
     * @return CurrentOffers
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
     * @return ArchivedOffers
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
    public function render()
    {
        if(!isset($this->config['transactionType'])){
            return;
        }

        if(!isset($this->config['category'])){
            return;
        }

        if(!isset($this->config['rand'])){
            return;
        }

        $order = [];
        if($this->config['rand'])
        {
            $order = 'rand';
        }

        $view = 'AppOfferBundle:Block:archivedOffers.html.twig';
        $offers = $this->services->get('offer.manager')->getCurrentOffersBy([
            'transactionType' => $this->config['transactionType']->getId(),
            'category' => $this->config['category']->getId()
        ], $order, 12, true);

        return $this->services->get('templating')
            ->render($view,array(
                'config' => $this->config,
                'offers' => $offers,
            ));
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return 'Blok Lista Archiwalnych Ofert';
    }

}