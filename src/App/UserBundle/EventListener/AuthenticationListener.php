<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\EventListener;

use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use App\UserBundle\Services\UserManager;
/**
 * Class AuthenticationListener
 *
 * @author wojciech przygoda
 */
class AuthenticationListener
{
    /**
     *
     * @var UserManager user manager
     */
    protected $userManager;
    /**
     * Constructor
     *
     * @param UserManager $userManager  user manager
     */
    public function __construct(UserManager $userManager) {
        $this->userManager = $userManager;
    }
    /**
     * onAuthenticationFailure event
     *
     * @param 	AuthenticationFailureEvent $event event
     */
    public function onAuthenticationFailure( AuthenticationFailureEvent $event )
    {
            // executes on failed login
    }

    /**
     * onAuthenticationSuccess event
     *
     * @param 	InteractiveLoginEvent $event event
     */
    public function onAuthenticationSuccess( InteractiveLoginEvent $event )
    {
        $user = $this->userManager->getCurrentLogged();
        $currIp = $event->getRequest()->getClientIp();
        $user->setLastLoginIp($currIp)
             ->setLastLogin(new \DateTime());
        $this->userManager->update($user);
    }
}
