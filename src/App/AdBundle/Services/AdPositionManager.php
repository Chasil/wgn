<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Class AdPositionManager
 *
 * @author wojciech przygoda
 */
class AdPositionManager {

    /**
     *
     * @var Container services container
     */
    private $services;

    /**
     * Constructor
     *
     * @param Container $container  services container
     */
    function __construct(Container $container) {
      $this->services = $container;
    }
    /**
     * Get all ad positions
     *
     * @param bool $isOfficePosition is office position
     * @return AdPosition[]
     */
    public function getAll($isOfficePosition = false){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppAdBundle:AdPosition');
        $request = $this->services->get('request');
        $qb = $repo->createQueryBuilder('p')
                   ->orderBy('p.name','DESC');
        if($isOfficePosition){
            $qb->where('p.isOfficePosition = 1');
        }else {
            $qb->where('p.isOfficePosition = 0');
        }
        $query = $qb->getQuery();
        return $query->getResult();
    }
    /**
     * Get first ad position
     *
     * @param bool $isOfficePosition is office ad position
     * @return AdPosition | null
     */
    public function getFirst($isOfficePosition = false) {
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppAdBundle:AdPosition');

        $qb = $repo->createQueryBuilder('p')
                   ->orderBy('p.name','ASC')
                   ->setMaxResults(1);
        if($isOfficePosition){
            $qb->where('p.isOfficePosition = 1');
        }else {
            $qb->where('p.isOfficePosition = 0');
        }
        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * Get last ad position
     * @param bool $isOfficePosition
     * @return AdPosition | null
     */
    public function getLast($isOfficePosition = false) {
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppAdBundle:AdPosition');

        $qb = $repo->createQueryBuilder('p')
                   ->orderBy('p.name','DESC')
                   ->setMaxResults(1);
        if($isOfficePosition){
            $qb->where('p.isOfficePosition = 1');
        }else {
            $qb->where('p.isOfficePosition = 0');
        }
        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * Save ad position
     *
     * @param AdPosition $position
     * @return boolean
     */
    public function save($position) {
        $em = $this->services->get('doctrine')->getManager();
        $em->persist($position);
        $em->flush();

        return true;
    }

    /**
     * Find ad position by id
     *
     * @param int $id
     * @return AdPosition
     */
    public function findById($id, $useCache = true)
    {
        if(!$useCache) {
            return  $position =  $this->services
                ->get('doctrine')
                ->getManager()
                ->getRepository('AppAdBundle:AdPosition')
                ->findOneById($id);
        }
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $cacheKey = "ads_position_by_id_".$id;
        $position = $cache->fetch($cacheKey);


        if ($position === false) {
            $position = $this->services
                                   ->get('doctrine')
                                   ->getManager()
                                   ->getRepository('AppAdBundle:AdPosition')
                                   ->findOneById($id);
            $cache->save($cacheKey,$position ,60+rand(1,20));
        }
        return $position;
    }

    /**
     * Find by key
     *
     * @param string $key
     * @return AdPosition
     */
    public function findByKey($key)
    {
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $cacheKey = "ads_position_".$key;

        $position = $cache->fetch($cacheKey);
        if ($position === false) {
            $position = $this->services->get('doctrine')->getManager()
                             ->getRepository('AppAdBundle:AdPosition')
                             ->findOneByUniqKey($key);
            $cache->save($cacheKey,$position ,$this->services->getParameter('cache_lifetime'));
        }

        return $position;
    }
    /**
     * Remove ad positon
     *
     * @param int $id
     * @return boolean
     */
    public function delete($id) {
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppAdBundle:AdPosition');
        $position = $repo->findOneById($id);

        $em->remove($position);
        $em->flush();

        return true;
    }
}

