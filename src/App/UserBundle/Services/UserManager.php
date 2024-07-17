<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\UserBundle\Model\SearchUsers;
use App\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use App\UserBundle\Model\Order;

/**
 * Class UserManager
 *
 * @author wojciech przygoda
 */
class UserManager {
    /**
     *
     * @var Container services container
     */
    private $services;
    /**
     *
     * @var SecurityContext security context
     */
    private $security;

    /**
     * Constructor
     *
     * @param Container $container services container
     * @param type $security security context
     */
    function __construct(Container $container, $security) {
      $this->services = $container;
      $this->security = $security;
    }
    /**
     * Get current logged user
     * @return User
     */
    public function getCurrentLogged(){
            if($this->security->getToken() &&
               $this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')){
                return $this->security->getToken()->getUser();
            }
            return null;
    }
    /**
     * Check if user is logged
     *
     * @return boolean
     */
    public function isLogged() {
        $user = $this->getCurrentLogged();

        if(is_object($user)){
            return true;
        }

        return false;
    }
    /**
     * Find user by signature
     *
     * @param string $signature signature
     * @return User
     */
    public function findBySignature($signature){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppUserBundle:User');

        return $repo->findOneBySignature($signature);
    }
    /**
     * Find user by email
     *
     * @param string $email email
     * @return User
     */
    public function findByEmail($email){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppUserBundle:User');

        return $repo->findOneByEmail($email);
    }
    /**
     * Find user by username
     *
     * @param string $username username
     * @return User
     */
    public function findByUsername($username){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppUserBundle:User');

        return $repo->findOneByUsername($username);
    }
    /**
     * Find user by hash
     *
     * @param string $hash hash
     * @return User
     */
    public function findBySecurityHash($hash){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppUserBundle:User');
        return $repo->findOneBySecurityHash($hash);
    }
    /**
     * Find user by id
     *
     * @param int $id user id
     * @return User
     */
    public function findById($id){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppUserBundle:User');
        return $repo->findOneById($id);
    }
    /**
     * Find agent by id
     *
     * @param int $id agent id
     * @return User
     */
    public function findAgentById($id){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppUserBundle:User');
        $agent = $repo->findOneBy(array('id'=>$id));

        if(!is_object($agent)){
             throw new NotFoundHttpException('Nie znaleziono!');
        }
        $roles = $agent->getRoles();
        if(!in_array('ROLE_AGENT',$roles)){
             throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $agent;
    }
    /**
     * Get all users
     *
     * @return User[]
     */
    public function getAll(){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppUserBundle:User');

        return $repo->findAll();
    }
    /**
     * Add user
     *
     * @param User $user user
     * @return boolean
     * @throws \Exception
     */
    public function add($user) {

        $factory = $this->services->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);

        $user->setPassword($encoder->encodePassword($user->getPlainPassword(),
                                                    $user->getSalt()));

        $em = $this->services->get('doctrine')->getManager();
        $em->getConnection()->beginTransaction();

        try {
            $this->save($user);
            $this->updateUserIdInOffers($user);
            $em->getConnection()->commit();
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();
            throw $ex;
        }


        return true;
    }
    /**
     * Update user id in offers
     *
     * @param User $user user
     */
    public function updateUserIdInOffers(User $user){
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppOfferBundle:Offer');

        $repo->updateUserId($user);
    }
    /**
     * Save user
     *
     * @param User $user user
     */
    public function save($user){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $this->services->get('doctrine')->getRepository('AppUserBundle:User');

        if(!$user->getId() && !is_null($user->getOffice())){
           $user->setOrdering($repo->getMaxOrdering($user->getOffice()->getId())+1);
        }elseif(is_null($user->getOffice()) && $user->isChange('office')){
            $changes = $user->getChanges();
            $repo->updateOrderingAfterDelete($user->getOrdering(),
                                             $changes['office'][0]->getId());
            $user->setOrdering($repo->getMaxOrdering($user->getOffice()->getId())+1);
        }
        $em->persist($user);
        $em->flush();
    }
    /**
     * Remove user
     *
     * @param int $id user id
     * @throws \Exception
     */
    public function delete($id){
        $em = $this->services->get('doctrine')->getManager();
        $em->getConnection()->beginTransaction();

        try {
            $user = $this->findById($id);
            $roles = $user->getRoles();

            if(in_array('ROLE_SUPER_ADMIN',$roles)){
                throw \Exception('Can\'t remove super administrator');
            }

            $em->getRepository('AppUserBundle:User')
               ->updateOrderingAfterDelete($user->getOrdering(),
                                             $user->getOffice()->getId());
            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollback();
            throw $e;
        }

        $em->remove($user);
        $em->flush();
    }
    /**
     * Update user
     *
     * @param User $user user
     */
    public function update($user){
        $em = $this->services->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
    }
    /**
     * Change user password
     *
     * @param User $user user
     */
    public function changePassword($user){
        $em = $this->services->get('doctrine')->getManager();
        $factory = $this->services->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
        $user->setpasswordRequestDate(null);
        $user->setSecurityHash(null);
        $user->setPassword($password);

        $em->persist($user);
        $em->flush();
    }
    /**
     * Send reset password message
     *
     * @param User $user user
     */
    public function sendResetMassage($user,$backOffice=false)
    {
        $template = 'AppUserBundle:Mail:resetPassword.html.twig';

        if($backOffice){
            $template = 'AppUserBundle:Mail:backOfficeResetPassword.html.twig';
        }
            $mailer = $this->services->get('mailer');
            $message = $mailer->createMessage()
                ->setSubject('Resetowanie Hasła - wgn.pl')
                ->setFrom(array($this->services->getParameter('mail_sender_email')=>$this->services->getParameter('mail_sender_name')))
                ->setTo($user->getEmail())
                ->setBody(
                    $this->services->get('templating')->render(
                        $template,
                        array('user'=>$user,)
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);
    }
    /**
     * Set last activity
     */
    function setLastActivity(){
        $user = $this->getCurrentLogged();

        if($user){
            $user->setLastActivity(new \DateTime());
            $this->update($user);
        }
    }
    /**
     * Find user by import id
     * @param int $id user import id
     * @return User
     */
    public function findByImportId($id){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppUserBundle:User');

        return $repo->findOneByImportId($id);
    }
    /**
     * Get users with pagination
     *
     * @param SearchUsers $searchUsers search params
     * @param boolean $all all
     * @return Paginator\User[]
     */
    public function getWidthPagination(SearchUsers $searchUsers,$all=false){
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')->getRepository('AppUserBundle:User');
        $qb = $repo->createQueryBuilder('u')
                   ->leftJoin('u.addresses', 'a')
                   ->leftJoin('u.companyData','c')
                   ->where('a.isDefault = 1 OR a.isDefault IS NULL')
                   ->addOrderBy('u.ordering','DESC')
                ;

        if($searchUsers->getType() == User::TYPE_AGENTS
                || $searchUsers->getType() == User::TYPE_OFFICE_MANAGER){
            $qb->leftJoin('u.office','o');
        }

        if($this->getCurrentLogged()->isOfficeManager()){
            $qb->andWhere('o.id = :officeId')
               ->setParameter('officeId', $this->getCurrentLogged()->getOffice()->getId());
        }elseif($request->query->has('idOffice')){
            $qb->andWhere('o.id = :officeId')
               ->setParameter('officeId', $request->get('idOffice'));
        }

        if($searchUsers->getRole() &&
                 $searchUsers->isRolePermitted()){

               $qb->andWhere('u.roles LIKE :role')
                  ->setParameter('role', '%'.$searchUsers->getRole().'%');
        }else {
            $orX = $qb->expr()->orX();
            foreach($searchUsers->getPermittedRoles() as $role){
                $orX->add($qb->expr()->like('u.roles',
                          $qb->expr()->literal('%'.$role.'%')));
            }

            $qb->andWhere($orX);
        }

        if($searchUsers->getUsername()){
           $qb->andWhere('u.username LIKE :username')
               ->setParameter('username', '%'.$searchUsers->getUsername().'%');
        }
        if($searchUsers->getEmail()){
           $qb->andWhere('u.email LIKE :email')
               ->setParameter('email', '%'.$searchUsers->getEmail().'%');
        }
        if($searchUsers->getName()){
            $orX = $qb->expr()->orX();
            $orX->addMultiple(array('u.firstName LIKE :name','u.lastName LIKE :name'));
            $qb->andWhere($orX)
               ->setParameter('name', '%'.$searchUsers->getName().'%');
        }
        if($searchUsers->getType() == User::TYPE_AGENTS &&
                $searchUsers->getOffice()){
            $qb->andWhere('o.name LIKE :office')
               ->setParameter('office', '%'.$searchUsers->getOffice().'%');

        }
        if($searchUsers->getType() == User::TYPE_CLIENTS &&
                $searchUsers->getCompany()){
            $qb->andWhere('c.name LIKE :office')
               ->setParameter('office', '%'.$searchUsers->getOffice().'%');

        }
        if($searchUsers->getType() == User::TYPE_CLIENTS &&
                $searchUsers->getNip()){
            $qb->andWhere('c.nip LIKE :nip')
               ->setParameter('nip', '%'.$searchUsers->getNip().'%');

        }
        if($searchUsers->getDateFrom()){
            $date = \DateTime::createFromFormat('Y-m-d', $searchUsers->getDateFrom());
            $date->setTime(00,00,00);
            $qb->andWhere('u.createDate >= :dateFrom')
               ->setParameter('dateFrom', $date);
        }
        if($searchUsers->getDateTo()){
            $date = \DateTime::createFromFormat('Y-m-d', $searchUsers->getDateTo());
            $date->setTime(23,59,59);
            $qb->andWhere('u.createDate <= :dateTo')
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
     * Change user enabled state
     *
     * @param int $id user id
     * @param boolean $enabled enabled state
     */
    public function changeEnabled($id, $enabled){

        $em = $this->services->get('doctrine')->getManager();

        $user = $this->findById($id);
        $user->setIsEnabled($enabled);
        $em->persist($user);
        $em->flush();
    }
    /**
     * Change user enabled office management
     *
     * @param  int $id user id
     * @param boolean $enabled enabled state
     */
    public function changeofficeManagement($id, $enabled){

        $em = $this->services->get('doctrine')->getManager();

        $user = $this->findById($id);
        $user->setIsOfficeManager($enabled);
        $em->persist($user);
        $em->flush();
    }
    /**
     * Set user uid
     *
     * @return string
     */
    public function setUid(){
        $request = $this->services->get('request');
        if($request->cookies->get('uid')){
            return $request->cookies->get('uid');
        }
        $uid = md5(uniqid(rand(), true));
        $response = new Response();
        $cookie = new Cookie('uid', $uid, time()+(3600*24*365*10));
        $response->headers->setCookie($cookie);
        $response->send();
        return $uid;
    }
    /**
     * Send offer
     *
     * @param Order $order order
     */
    public function sendOrder(Order $order)
    {
            $mailer = $this->services->get('mailer');
            $message = $mailer->createMessage()
                ->setSubject('Zamówienie Pakietu - wgn.pl')
                ->setFrom(array($this->services->getParameter('mail_sender_email')=>$this->services->getParameter('mail_sender_name')))
                ->setTo([$this->services->getParameter('mail_order_email'), $this->services->getParameter('mail_order_email_2')])
                ->setBody(
                    $this->services->get('templating')->render(
                        'AppUserBundle:Mail:order.html.twig',
                        array('order'=>$order,)
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);
    }
    /**
     * Change article ordering
     *
     * @param int $id article id
     * @param string $direction change ordering direction
     * @return boolean
     * @throws \Exception
     */
    public function changeOrdering($id,$direction) {

        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppUserBundle:User');
        $em->getConnection()->beginTransaction();

        try {
            $user = $this->findById($id);

            switch($direction){
                case 'up':
                    $prev = $repo->getPrev($user);

                    $user->incrementOrdering();
                    $prev->decrementOrdering();

                    $em->persist($prev);
                    $em->persist($user);
                    $em->flush();
                break;

                case 'down':
                    $next = $repo->getNext($user);

                    $user->decrementOrdering();
                    $next->incrementOrdering();

                    $em->persist($next);
                    $em->persist($user);
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
}
