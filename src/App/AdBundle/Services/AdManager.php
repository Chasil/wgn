<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class AdManager
 *
 * @author wojciech przygoda
 */
class AdManager {

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
     * Get all ad positions
     * @return AdPosition[]
     */
    public function getAllPositions(){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppAdBundle:AdPosition');

        return $repo->findAll();
    }

    /**
     * Get all ads
     *
     * @return Ad[]
     * @throws AccessDeniedException
     */
    public function getAll(){

        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppAdBundle:Ad');

        $qb = $repo->createQueryBuilder('a')
                   ->join('a.position','p')
                   ->where('p.id = :id')
                   ->addOrderBy('a.ordering','DESC');

        $isOfficePosition = false;

        if($request->query->has('idOffice')){
            $isOfficePosition = true;
            $idOffice = $request->query->get('idOffice');
            $qb->join('a.office','o')
               ->andWhere('o.id = :idOffice')
               ->setParameter('idOffice', $idOffice);
        }

        if($request->query->has('idPosition')){
            $qb->setParameter('id', $request->query->get('idPosition'));
            $position = $this->services
                             ->get('adposition.manager')
                             ->findById($request->query->get('idPosition'));
        }else {
            $position = $this->services
                             ->get('adposition.manager')
                             ->getLast($isOfficePosition);

            $qb->setParameter('id', $position->getId());
            $request->query->set('cat',$position->getId());
        }

        if (false ===  $this->services->get('security.context')->isGranted('manage', $position)) {
            throw new AccessDeniedException('Access Denid');
        }
        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
        $request->query->get('pp', 25)/*limit per page*/
        );
    }

    /**
     * Get all published ads by position id
     *
     * @param id $idPosition id ad position
     * @return Ad[]
     */
    public function getAllPublishByPosition($idPosition)
    {
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $cacheKey = "all_publish_ads_position_".$idPosition;

        $ads = $cache->fetch($cacheKey);
        if ($ads === false) {
            $ads = $this->services->get('doctrine')->getManager()
                ->getRepository('AppAdBundle:Ad')
                ->findPublishByPositionId($idPosition);
            $cache->save($cacheKey,$ads , 1);
        }



        return $ads;

    }

    /**
     * Get all published ads by position id and office id
     *
     * @param int $idPosition id ad position
     * @param int $idOffice id office
     * @return Ad[]
     */
    public function getAllPublishByPositionAndOffice($idPosition,$idOffice)
    {
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $cacheKey = "all_publish_ads_position_and office_id_".$idPosition .'_'.$idOffice;
        $ads = $cache->fetch($cacheKey);
        if ($ads === false) {
            $ads = $this->services->get('doctrine')->getManager()
                ->getRepository('AppAdBundle:Ad')
                ->findPublishByPositionIdAndOfficeId($idPosition,$idOffice);
            $cache->save($cacheKey,$ads , 60+rand(1,20));
        }

        return $ads;

    }

    /**
     * Add ad
     *
     * @param Ad $ad ad
     * @return boolean
     */
    public function add($ad) {
        $this->save($ad);

        return true;
    }

    /**
     * Save ad
     * @param Ad $ad ad
     * @return boolean
     */
    public function save($ad) {
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppAdBundle:Ad');

        if(!$ad->getId()){
            $maxOrdering = $repo->findMaxOrdering($ad->getPosition()->getId())+1;
            $ad->setOrdering($maxOrdering);
        }elseif($ad->isChange('position')){
            $changes = $ad->getChanges();
            $em->getRepository('AppAdBundle:Ad')
               ->updateOrderingAfterDelete($ad->getOrdering(),
                                           $changes['position'][0]->getId());

            $maxOrdering = $repo->findMaxOrdering($ad->getPosition()->getId())+1;
            $ad->setOrdering($maxOrdering);
        }

        $em->persist($ad);
        $em->flush();

        return true;
    }

    /**
     * Find ad by id
     *
     * @param int $id ad id
     * @return Ad[]
     * @throws NotFoundHttpException
     */
    public function findById($id)
    {
        $em = $this->services->get('doctrine')->getManager();
        $ad = $em->getRepository('AppAdBundle:Ad')
                 ->findOneById($id);

        if(!is_object($ad)){
            throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $ad;
    }

    /**
     * Change ad state
     *
     * @param int $id ad id
     * @param int $state ad state
     * @return boolean
     */
    public function changeState($id,$state) {

        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppAdsBundle:Ad');
        $repo->changeState($id,$state);

        return true;
    }

    /**
     * Change ad ordering
     *
     * @param int $id ad id
     * @param string $direction ad ordering direction change
     * @return boolean
     * @throws \Exception
     */
    public function changeOrdering($id,$direction) {

        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppAdBundle:Ad');
        $em->getConnection()->beginTransaction();

        try {
            $ad = $this->findById($id);

            switch($direction){
                case 'up':
                    $prevAd = $repo->findPrev($ad);

                    $ad->incrementOrdering();
                    $prevAd->decrementOrdering();

                    $em->persist($prevAd);
                    $em->persist($ad);
                    $em->flush();
                break;

                case 'down':
                    $nextAd = $repo->findNext($ad);
                    $ad->decrementOrdering();
                    $nextAd->incrementOrdering();

                    $em->persist($nextAd);
                    $em->persist($ad);
                    $em->flush();

                break;
            }
            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollback();
            throw $e;
        }

        return true;
    }
    /**
     * Remove ad
     *
     * @param int $id ad id
     * @throws \Exception
     */
    public function remove($id){
        $em = $this->services->get('doctrine')->getManager();
        $em->getConnection()->beginTransaction();

        try {
            $ad = $this->findById($id);

            $em->remove($ad);
            $em->flush();
            $em->getRepository('AppAdBundle:Ad')
               ->updateOrderingAfterDelete($ad->getOrdering(),
                                           $ad->getPosition()->getId());

            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * Increment ad clicks
     *
     * @param int $id ad id
     * @return Ad
     */
    public function incrementClicks($id){
        $em = $this->services->get('doctrine')->getManager();
        $ad = $this->findById($id);

        $ad->incrementClicks();
        $em->persist($ad);
        $em->flush();
        return $ad;
    }

    /**
     * Increment ad hits
     *
     * @param int $id ad id
     * @return int
     */
    public function incrementHits($id){
        $repo = $this->services->get('doctrine')->getManager()
                     ->getRepository('AppAdBundle:Ad');

        $repo->incrementHits($id);

        return $id;
    }
}

