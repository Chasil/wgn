<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Services;

use App\OfferBundle\Entity\Message;
use App\OfferBundle\Model\Contact;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
/**
 * Class MessageManager
 *
 * @author wojciech przygoda
 */
class MessageManager {
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
     * Send message
     *
     * @param Message $message message
     */
    public function send(Message $message){
        $em = $this->services->get('doctrine')->getManager();
        $em->persist($message);
        $em->flush();
    }
    /**
     * Send message using contact data
     * @param Contact $contact contact
     * @return null
     */
    public function sendUsingContactData(Contact $contact){

        $message = new Message();
        $offer = $contact->getOffer();

        if(!$offer->getUser()){
            return;
        }
        $message->setOffer($offer)
                ->setSubject($contact->getSubject())
                ->setContent($contact->getMessage())
                ->setRecipient($offer->getUser())
                ->setPhone($contact->getPhone())
                ->setEmail($contact->getEmail());


        $userManager = $this->services->get('user.manager');
        $user = $userManager->getCurrentLogged();

        if($user){
            $message->setSender($user);
        }

        $this->send($message);
    }
    /**
     * Get messages by user with pagination
     * @return Paginator
     */
    public function getByUserWidthPagination(){
        $userManager = $this->services->get('user.manager');
        $user = $userManager->getCurrentLogged();
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')->getRepository('AppOfferBundle:Message');
        $qb = $repo->createQueryBuilder('m')
                      ->join('m.recipient', 'r')
                      ->join('m.offer','o')
                      ->where('r.id = :id')
                      ->setParameter('id',$user->getId())
                      ->orderBy('m.createDate','DESC');


       if($request->query->has('signature')){
           $qb->andWhere('o.signature LIKE :signature')
               ->setParameter('signature', '%'.$request->query->get('signature').'%');
        }
        $permitedColumns = array('createDate','price','pricem2','squere');
        $permitedDirs = array('ASC','DESC');

        $orderParts = explode('_',$request->query->get('order','createDate_DESC'));

        if(in_array($orderParts[0], $permitedColumns) &&
                in_array($orderParts[1], $permitedDirs)){

            $qb->orderBy('m.'.$orderParts[0],$orderParts[1]);
        }

        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
            $request->query->get('pp', 20)/*limit per page*/
        );
    }
    /**
     * Find message by id
     *
     * @param int $id message id
     * @return Message
     */
    public function findById($id) {
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppOfferBundle:Message');

        return $repo->findOneById($id);

    }
    /**
     * Get message details
     *
     * @param int $id message id
     * @return null|Message
     */
    public function getDetails($id) {
        $message = $this->findById($id);

        if ( $this->services->get('security.context')->isGranted('view',$message) ){
            return $message;
        }

        return null;
    }
}
