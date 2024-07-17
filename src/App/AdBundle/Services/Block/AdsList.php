<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Services\Block;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\AppBundle\Services\Block\BlockInterface;
use App\AppBundle\Entity\BlockElement;
use App\AdBundle\Entity\AdPosition;

/**
 * Class AdsList
 *
 * @author wojciech przygoda
 */
class AdsList implements BlockInterface {

    /**
     *
     * @var Container services container
     */
    private $services;

    /**
     *
     * @var array config
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
     * @param array $config
     * @return AdsList
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
     * Set element
     *
     * @param BlockElement $element
     * @return AdsList
     */
    public function setElement(BlockElement $element) {
        $this->element = $element;
        return $this;
    }

    /**
     * Get element
     *
     * @return BlockElement
     */
    public function getElement() {
        return $this->element;
    }

    /**
     * Render view
     *
     * @return string
     */
    public function render() {

        $ads = $this->getAds();
        $position = $this->getPosition();
        if(!$ads){
            return;
        }
        if(!isset($this->config['view'])){
            $view = 'AppAdBundle:Block:adsBox.html.twig';
        }else {
            $view = 'AppAdBundle:Block:'.$this->config['view'].'.html.twig';
        }


        return $this->services->get('templating')
                    ->render($view,
                    array('ads' => $ads,
                          'position' => $position,
                          'config'=>$this->config,
                          'element'=>$this->element));
    }
    private function getPosition() {
        if(!isset($this->config['idPosition'])){
            return false;
        }
        $position = $this->services->get('adposition.manager')->findById($this->config['idPosition']);
        if(!is_object($position)){
            return;
        }

        return $position;
    }
    /**
     * Get ads
     *
     * @return null|array
     */
    private function getAds(){
        $ads = [];
        $position = $this->getPosition();
        $total = $position->getAdsLimit();


        if($position->getIsOfficePosition()){

            $subdomain = $this->services->get('request')->get('subdomain');

            if($subdomain !=''){
                $idOffice = $this->services->get('office.manager')->findBySubdomain($subdomain)->getId();
            }else {
                $idOffice = $this->services->get('request')->get('id');
            }


            $ads = $this->services->get('ad.manager')
                         ->getAllPublishByPositionAndOffice($this->config['idPosition'],
                                                            $idOffice);
        }else {
            $ads = $this->services->get('ad.manager')
                    ->getAllPublishByPosition($this->config['idPosition']);
        }

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

        return $selectedAds;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(){
        return 'Blok Reklam';
    }
}

