<?php
/**
 * This file is part of the AppSettingsBundle package.
 *
 */
namespace App\SettingsBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\SettingsBundle\Entity\Settings;

/**
 * Class SettingsManager
 *
 * @author wojciech przygoda
 */
class SettingsManager {
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
     * Save settings
     *
     * @param Settings $settings settings
     * @return boolean
     */
    public function save($settings) {
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $em = $this->services
             ->get('doctrine')
             ->getManager()
        ;

        $em->persist($settings);
        $em->flush()
        ;
        $cache->delete('current_settings');
        return true;
    }
    /**
     * Get settings
     *
     * @return Settings
     */
    public function get()
    {
        $settings = $this->services->get('doctrine')->getManager()
                         ->getRepository('AppSettingsBundle:Settings')
                         ->findCurrent();


        if(!is_object($settings)){
            $settings = new Settings();
        }

        return $settings;
    }
}

