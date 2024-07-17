<?php
/**
 * This file is part of the AppNewsletterBundle package.
 *
 */
namespace App\NewsletterBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\NewsletterBundle\Model\SearchEmails;
use App\NewsletterBundle\Entity\Newsletter;
use App\NewsletterBundle\Model\Message;
use App\NewsletterBundle\Entity\NewsletterMessage;
use App\NewsletterBundle\Entity\LookingFor;

/**
 * Class NewsletterManager
 *
 * @author wojciech przygoda
 */
class NewsletterManager {
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
     * Get all newsletter subscriptions
     *
     * @param SearchEmails $searchEmails
     * @param bool $all
     * @return Newsletter[]|Paginator
     */
    public function getWidthPagination(SearchEmails $searchEmails,$all=false){
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppNewsletterBundle:Newsletter');
        $qb = $repo->createQueryBuilder('n')
                   ->orderBy('n.createDate','DESC')
                ;


        if($searchEmails->getEmail()){
           $qb->andWhere('n.email LIKE :email')
               ->setParameter('email', '%'.$searchEmails->getEmail().'%');
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
     * Get Recipients
     *
     * @return Newsletter[]
     */
    public function getRecipients() {
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppNewsletterBundle:Newsletter');

        return $repo->findByIsActive(true);
    }
    /**
     * Add newsletter subscription
     *
     * @param Newsletter $newsletter newsletter subscription
     */
    public function add(Newsletter $newsletter){
        $ip = $this->services->get('request')->getClientIp();
        $newsletter->setIp($ip);
        $this->save($newsletter);

        $mailer = $this->services->get('mailer');

        $message = $mailer->createMessage()
            ->setSubject('Potwierdzenie newslettera')
            ->setFrom(array($this->services->getParameter('mail_sender_email')=>$this->services->getParameter('mail_sender_name')))
            ->setTo($newsletter->getEmail())
            ->setBody(
                $this->services->get('templating')->render(
                    'AppNewsletterBundle:Emails:confirm.html.twig',
                    array('code'=>$newsletter->getHash(),)
                ),
                'text/html'
            )
        ;

        $mailer->send($message);
    }
    /**
     * Save newsletter subscription
     *
     * @param Newsletter $newsletter  newsletter subscription
     */
    public function save(Newsletter $newsletter){
        $em = $this->services->get('doctrine')->getManager();
        $em->persist($newsletter);
        $em->flush();
    }
    /**
     * Save lookingFor subscription
     *
     * @param LookingFor $lookingFor lookingFor subscription
     */
    public function saveLookingFor(LookingFor $lookingFor){
        $em = $this->services->get('doctrine')->getManager();
        $ip = $this->services->get('request')->getClientIp();
        $lookingFor->setIp($ip);
        $em->persist($lookingFor);
        $em->flush();
    }

    /**
     * Send newsletter
     *
     * @param Message $message newsletter message
     */
    public function send(Message $message){
        $resipients = $this->getRecipients();
         $mailer = $this->services->get('mailer');

        foreach($resipients as $resipient){
            $newsletter = $mailer->createMessage()
                ->setSubject($message->getSubject())
                ->setFrom(array($this->services
                                     ->getParameter('mail_sender_email')
                                =>$this->services
                                       ->getParameter('mail_sender_name')))
                ->setTo($resipient->getEmail())
                ->setBody(
                    $this->services->get('templating')->render(
                        'AppNewsletterBundle:Emails:newsletter.html.twig',
                        array('newsletter'=>$message,
                              'code'=>$resipient->getHash(),
                             )
                    ),
                    'text/html'
                )
            ;
            $mailer->send($newsletter);
        }
        $newsletterMessage = new NewsletterMessage();
        $newsletterMessage->setSubject($message->getSubject());
        $newsletterMessage->setContent($message->getMessage());

        $em = $this->services->get('doctrine')->getManager();
        $em->persist($newsletterMessage);
        $em->flush();
    }
    public function getSentMessages() {
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppNewsletterBundle:NewsletterMessage');
        $qb = $repo->createQueryBuilder('m')
                   ->orderBy('m.sendDate','DESC')
                ;

        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
            $request->query->get('pp', 25)/*limit per page*/
        );
    }
    /**
     * Send lookingfor newsletter
     *
     * @param LookingFor $subscription lookingFor subscription
     * @param Offer[] $offers
     */
    public function sendLookingFor(LookingFor $subscription, $offers){

        $mailer = $this->services->get('mailer');

        $newsletter = $mailer->createMessage()
            ->setSubject('nowe oferty ze strony wgn.pl')
            ->setFrom(array($this->services
                                 ->getParameter('mail_sender_email')
                            =>$this->services
                                   ->getParameter('mail_sender_name')))
            ->setTo($subscription->getEmail())
            ->setBody(
                $this->services->get('templating')->render(
                    'AppNewsletterBundle:Emails:lookingFor.html.twig',
                    array('offers'=>$offers,
                          'code'=>$subscription->getHash(),
                         )
                ),
                'text/html'
            )
        ;
            $mailer->send($newsletter);

    }
    /**
     * Get all looking for subscriptions
     *
     * @param bool $onlyActive only active subscriptions
     * @return LookingFor[]
     */
    public function getAllLookingFor($onlyActive=true){
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppNewsletterBundle:LookingFor');

        if($onlyActive){
            return $repo->findByIsActive(true);
        }

        return $repo->findAll();
    }
    /**
     * Get newsletter subscription by id
     *
     * @param int $id newsletter subscription id
     * @return Newsletter
     */
    public function findById($id) {
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppNewsletterBundle:Newsletter');
        return $repo->findOneById($id);
    }
    /**
     * Get newsletter subscription by hash
     *
     * @param string $hash subscription hash
     * @return Newsletter
     */
    public function findByHash($hash) {
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppNewsletterBundle:Newsletter');
        return $repo->findOneByHash($hash);
    }

    /**
     * Get looking for subscription by hash
     *
     * @param string $hash subscription hash
     * @return LookingFor
     */
    public function findLookingForByHash($hash) {
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppNewsletterBundle:LookingFor');
        return $repo->findOneByHash($hash);
    }

    /**
     * Remove newsletter subscription by id
     *
     * @param int $id newsletter subscription id
     */
    public function remove($id) {
        $email = $this->findById($id);
        $em = $this->services->get('doctrine')->getManager();
        $em->remove($email);
        $em->flush();
    }
    /**
     * Activate subscription by hash
     * @param string $hash subscription hash
     */
    public function activateByHash($hash){
        $email = $this->findByHash($hash);
        $email->setIsActive(true);
        $em = $this->services->get('doctrine')->getManager();
        $em->persist($email);
        $em->flush();
    }
    /**
     * Deactivate newsletter subscription by hash
     * @param string $hash subscription hash
     */
    public function deactivateByHash($hash){
        $email = $this->findByHash($hash);
        $email->setIsActive(false);
        $em = $this->services->get('doctrine')->getManager();
        $em->persist($email);
        $em->flush();
    }
    /**
     * Deactivate loooking for subscription by hash
     * @param string $hash subscription hash
     */
    public function deactivateLookingForByHash($hash){
        $email = $this->findLookingForByHash($hash);
        $email->setIsActive(false);
        $em = $this->services->get('doctrine')->getManager();
        $em->persist($email);
        $em->flush();
    }
    /**
     * Change active
     *
     * @param int $id newsletter subscription id
     * @param bool $active active state
     */
    public function changeActive($id, $active){

        $em = $this->services->get('doctrine')->getManager();

        $newsletter = $this->findById($id);
        $newsletter->setIsActive($active);
        $em->persist($newsletter);
        $em->flush();
    }
}
