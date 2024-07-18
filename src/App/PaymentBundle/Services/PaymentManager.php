<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\PaymentBundle\Entity\Payment;
use App\PaymentBundle\Model\SearchPayments;
use App\UserBundle\Entity\User;

/**
 * Class PaymentManager
 *
 * @author wojciech przygoda
 */
class PaymentManager {
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
     * Process payment
     *
     * @param Payment $payment payment
     * @param string $type type
     */
    function process(Payment $payment, $type){
        $userManager = $this->services->get('user.manager');
        $payment->setUser($userManager->getCurrentLogged())
                ->setSessionId(uniqid('',true));
        $this->save($payment);
        return $this->getService($payment)->process($payment, $type);

    }
    /**
     * Save payment
     *
     * @param Payment $payment payment
     */
    public function save(Payment $payment){
        $em = $this->services->get('doctrine')->getManager();
        $em->persist($payment);
        $em->flush();
    }
    /**
     * Get payment by id
     *
     * @param int $id payment id
     * @return Payment
     */
    public function findById($id) {
        return $this->services->get('doctrine')->getManager()
                    ->getRepository('AppPaymentBundle:Payment')->findOneById($id);
    }
    /**
     * Get payment by session id
     *
     * @param string $sessionId session id
     * @return Payment
     */
    public function findBySessionId($sessionId) {
        return $this->services->get('doctrine')->getManager()
                    ->getRepository('AppPaymentBundle:Payment')->findOneBySessionId($sessionId);
    }
    /**
     * Get payment method service
     *
     * @param Payment $payment payment
     * @return PaymentMethodInterface
     * @throws \Exception
     */
    private function getService(Payment $payment){
        $serviceName = $payment->getPaymentMethod() . '.manager';

        if($this->services->has($serviceName)){
            return $this->services->get($serviceName);
        }else{
            throw new \Exception('Payment service "'.$serviceName.'" not found');
        }
    }
    /**
     * Get payments with pagination
     *
     * @param SearchPayments $searchPayments query params
     * @param bool $all get all
     * @return Payment[]
     */
    public function getWidthPagination(SearchPayments $searchPayments,$all=false){
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')->getRepository('AppPaymentBundle:Payment');
        $qb = $repo->createQueryBuilder('p')
                   ->leftJoin('p.user', 'u')
                   ->leftJoin('p.offer', 'o')
                   ->addOrderBy('p.createDate','DESC')
                ;

        if($searchPayments->getPaymentMethod()){
               $qb->andWhere('p.paymentMethod LIKE :paymentMethod')
                  ->setParameter('paymentMethod', '%'.$searchPayments->getPaymentMethod().'%');
        }
        if($searchPayments->getState()){
               $qb->andWhere('p.state = :state')
                  ->setParameter('state', $searchPayments->getState());
        }
        if($searchPayments->getSignature()){
           $qb->andWhere('o.signature LIKE :signature')
               ->setParameter('signature', '%'.$searchPayments->getSignature().'%');
        }
        if($searchPayments->getDateFrom()){
            $date = \DateTime::createFromFormat('Y-m-d', $searchPayments->getDateFrom());
            $date->setTime(00,00,00);
            $qb->andWhere('p.createDate >= :dateFrom')
               ->setParameter('dateFrom', $date);
        }
        if($searchPayments->getDateTo()){
            $date = \DateTime::createFromFormat('Y-m-d', $searchPayments->getDateTo());
            $date->setTime(23,59,59);
            $qb->andWhere('p.createDate <= :dateTo')
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
     * Get user payments with pagination
     *
     * @param User $user user
     * @return Payment
     */
    public function getUserWidthPagination(User $user = null){

        if(!$user){
            $userManager = $this->services->get('user.manager');
            $user = $userManager->getCurrentLogged();
        }
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')->getRepository('AppPaymentBundle:Payment');
        $qb = $repo->createQueryBuilder('p')
                   ->leftJoin('p.user', 'u')
                   ->leftJoin('p.offer', 'o')
                   ->where('u.id = :user')
                   ->setParameter('user',$user->getId())
                   ->addOrderBy('p.createDate','DESC')
                ;
        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
            $request->query->get('pp', 25)/*limit per page*/
        );

    }
    /**
     * Get payment details
     * 
     * @param int $id payment id
     * @return Payment|null
     */
    public function getDetails($id) {
        $payment = $this->findById($id);

        if ( $this->services->get('security.context')->isGranted('view',$payment) ){
            return $payment;
        }

        return null;
    }
}