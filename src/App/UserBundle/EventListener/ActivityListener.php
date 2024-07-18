<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
/**
 * Class ActivityListener
 *
 * @author wojciech przygoda
 */
class ActivityListener
{
    /**
     *
     * @var Container services container
     */
    protected $container;

    /**
     * Constructor
     *
     * @param Container $container services container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

    }

    /**
     * OnCoreController event
     * On each request we want to update the user's last activity datetime
     *
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event event
     * @return void
     */
    public function onCoreController(FilterControllerEvent $event)
    {
        $currentUrl = $this->container->get('request')->getPathInfo();
        $user = $this->container->get('user.manager')->getCurrentLogged();
        if(!$user){
            return;
        }
        if(preg_match('/^\/backoffice/',$currentUrl) && $user->isAgent() && !$user->isOfficeManager()){
            throw new AccessDeniedException('Brak Dostępu');
        }

        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST) {
            return;
        }
        try{
            $this->container->get('user.manager')->setLastActivity();
            $this->container->get('user.manager')->setUid();

        } catch (Exception $e) {
            throw $e;
        }

    }
}

