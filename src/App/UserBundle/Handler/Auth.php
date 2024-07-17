<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Auth
 *
 * @author wojciech przygoda
 */
class Auth implements AuthenticationFailureHandlerInterface, AuthenticationSuccessHandlerInterface, LogoutSuccessHandlerInterface
{
    /**
     *
     * @var Router router
     */
    protected $router;
    /**
     *
     * @var UserManager user manager
     */
    protected $userManager;

    /**
     * Constructor
     *
     * @param Router $router router
     * @param UserManager $userManager user manager
     */
    public function __construct(Router $router, $userManager)
    {
        $this->router = $router;
        $this->userManager = $userManager;
    }
    /**
     * OnAuthenticationFailure event
     *
     * @param Request $request request
     * @param AuthenticationException $exception exception
     * @return RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

       return new RedirectResponse($this->router->generate('login'));
    }

    /**
     * OnAuthenticationSuccess event
     *
     * @param Request $request request
     * @param TokenInterface $token token
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        return new RedirectResponse($this->router->generate('app_user_dashboard'));

    }
    /**
     * OnLogoutSuccess event
     * 
     * @param Request $request request
     */
    public function onLogoutSuccess(Request $request)
    {
        //return new RedirectResponse($this->router->generate('login'));
    }


}
?>
