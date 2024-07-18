<?php
/**
 * This file is part of the AppSubscriptionBundle package.
 *
 */
namespace App\SubscriptionBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\SubscriptionBundle\Entity\Subscription;
use App\SubscriptionBundle\Model\SearchSubscriptions;

/**
 * Class SubscriptionManager
 *
 * @author wojciech przygoda
 */
class SubscriptionManager {
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
     * Save subscription
     *
     * @param Subscription $subscription subscription
     * @return boolean
     */
    public function save(Subscription $subscription) {
        $em = $this->services->get('doctrine')->getManager();
        $em->persist($subscription);
        $em->flush();
    }
    /**
     * Reduce available offers
     *
     * @param Subscription $subscription subscription
     */
    public function reduceAvailable(Subscription $subscription){
        $subscription->reduceAvailable();
        $this->save($subscription);
    }
    /**
     * Get all subscriptions with pagination
     *
     * @param SearchSubscriptions $searchSubscriptions search params
     * @param boolean $all all
     * @return Paginator
     */
    public function getWidthPagination(SearchSubscriptions $searchSubscriptions,$all=false){
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppSubscriptionBundle:Subscription');
        $qb = $repo->createQueryBuilder('s')
                   ->leftJoin('s.user', 'u')
                   ->orderBy('u.createDate','DESC')
                ;

        if($searchSubscriptions->getState() == Subscription::STATE_ACTIVE){
            $qb->andWhere('s.expireDate >= :now AND (s.numberOfUsed < s.numberOfAvailable OR s.numberOfAvailable < 0)')
               ->setParameter('now', new \DateTime());
        }elseif($searchSubscriptions->getState() == Subscription::STATE_UNACTIVE){
            $qb->andWhere('s.expireDate <= :now OR (s.numberOfUsed >= s.numberOfAvailable AND s.numberOfAvailable > 0 )')
               ->setParameter('now', new \DateTime());
        }

        if($searchSubscriptions->getUsername()){
           $qb->andWhere('u.username LIKE :username')
               ->setParameter('username', '%'.$searchSubscriptions->getUsername().'%');
        }
        if($searchSubscriptions->getDateFrom()){
            $date = \DateTime::createFromFormat('Y-m-d', $searchSubscriptions->getDateFrom());
            $date->setTime(00,00,00);
            $qb->andWhere('s.createDate >= :dateFrom')
               ->setParameter('dateFrom', $date);
        }
        if($searchSubscriptions->getDateTo()){
            $date = \DateTime::createFromFormat('Y-m-d', $searchSubscriptions->getDateTo());
            $date->setTime(23,59,59);
            $qb->andWhere('s.createDate <= :dateTo')
               ->setParameter('dateTo', $date);
        }
        if($all){
            return $qb->getQuery()->getResult();
        }
        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
            $request->query->get('pp', 25)/*limit per page*/
        );
    }
    /**
     * Get subscription by id
     *
     * @param int $id subscription id
     * @return Subscription
     */
    public function findById($id) {
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppSubscriptionBundle:Subscription');

        return $repo->findOneById($id);
    }
    /**
     * Remove subscription
     * 
     * @param int $id subscription id
     */
    public function remove($id){
        $em = $this->services->get('doctrine')->getManager();
        $subscription = $this->findById($id);
        $em->remove($subscription);
        $em->flush();

    }
}
