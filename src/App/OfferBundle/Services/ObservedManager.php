<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\OfferBundle\Entity\Observed;
/**
 * Class ObservedManager
 *
 * @author wojciech przygoda
 */
class ObservedManager {
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
     * Add to observed
     *
     * @param int $id observed offer id
     * @return null
     */
    public function add($id) {
        $offerManager = $this->services->get('offer.manager');
        $userManager = $this->services->get('user.manager');
        $request = $this->services->get('request');
        $em = $this->services->get('doctrine')->getManager();

        $offer = $offerManager->findById($id);

        if($this->isObserved($offer)){
            return;
        }

        $observed = new Observed();
        $observed->setHash($request->cookies->get('uid'));
        $observed->setOffer($offer);
        $user = $userManager->getCurrentLogged();
        if($user){
            $observed->setUser($user);
        }

        $em->persist($observed);
        $em->flush();
    }
    /**
     * Remove by hash
     *
     * @param int $id observed offer id
     */
    public function removeByHash($id) {
        $offerManager = $this->services->get('offer.manager');
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppOfferBundle:Observed');
        $request = $this->services->get('request');
        $offer = $offerManager->findById($id);
        $observed = $repo->findOneBy(array('hash'=>$request->cookies->get('uid'),
                                        'offer'=>$offer));

        $em->remove($observed);
        $em->flush();
    }
    /**
     * Check if offer is add to observed
     *
     * @param Offer $offer offer
     * @return boolean
     */
    public function isObserved($offer) {
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppOfferBundle:Observed');
        $request = $this->services->get('request');

        $observed = $repo->findOneBy(array('hash'=>$request->cookies->get('uid'),
                                        'offer'=>$offer));

        if(is_object($observed)){
            return true;
        }

        return false;
    }
    /**
     * Count offers
     *
     * @return int
     */
    public function countObserved() {
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppOfferBundle:Observed');
        $request = $this->services->get('request');

        $observed = $repo->findBy(array('hash'=>$request->cookies->get('uid')));

        return count($observed);
    }
    /**
     * Get observed offers by user with pagination
     * @return Paginator
     */
    public function getByUserWidthPagination(){
        $userManager = $this->services->get('user.manager');
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppOfferBundle:Offer');

        $qb = $repo->createQueryBuilder('o')
                      ->leftJoin('o.observed', 'ob')
                      ->where('ob.hash = :hash')
                      ->setParameter('hash',$request->cookies->get('uid'))
                      ->orderBy('o.createDate','DESC');


       if($request->query->has('signature')){
           $qb->andWhere('o.signature LIKE :signature')
               ->setParameter('signature', '%'.$request->query->get('signature').'%');
        }
        $permitedColumns = array('createDate','price','pricem2','squere');
        $permitedDirs = array('ASC','DESC');

        $orderParts = explode('_',$request->query->get('order','createDate_DESC'));

        if(in_array($orderParts[0], $permitedColumns) &&
                in_array($orderParts[1], $permitedDirs)){

            $qb->orderBy('o.'.$orderParts[0],$orderParts[1]);
        }

        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
            $request->query->get('pp', 20)/*limit per page*/
        );
    }
}
