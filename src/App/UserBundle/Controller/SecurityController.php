<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use App\UserBundle\Entity\User;
use App\UserBundle\Form\ResetPasswordType;
use App\UserBundle\Form\RegistrationType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class SecurityController
 *
 * @author wojciech przygoda
 */
class SecurityController extends Controller
{
    /**
     * Login user
     * @return Response
    */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $user = new User();
        $userManager = $this->get('user.manager');
        $user->setRoles(array('ROLE_USER'));
        $form = $this->createForm(new RegistrationType(),$user);

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $userManager->add($user);
            $token = new UsernamePasswordToken($user, $user->getPassword(), "secured_area", $user->getRoles());
            $this->get("security.token_storage")->setToken($token);
            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

            return $this->redirect($this->generateUrl('user_account_start'));
        }
        return $this->render(
            'AppUserBundle:Security:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
                'form'          => $form->createView()
            )
        );
    }
    /**
     * Reset user password
     *
     * @param Request $request request
     * @return Response
    */
    public function resetPasswordAction(Request $request){
        $userManager = $this->get('user.manager');
        $error = null;
        if ($request->isMethod('POST')) {
            $user = $userManager->findByEmail($request->get('email'));

            if(null===$user){
                $error = 'Podany adres e-mail nie istnieje w bazie.';

                return $this->render('AppUserBundle:Security:resetPassword.html.twig',array(
                'last_username' => $request->get('email'),
                'error'=>$error,
                ));
            }
            if($user->isPasswordRequestNonExpired(60*60*24)){
                $error = 'Hasło było resetowane w ciągu 24h.';

                return $this->render('AppUserBundle:Security:resetPassword.html.twig',array(
                'last_username' => $request->get('email'),
                'error'=>$error,
                ));
            }else {
                $user->setPasswordRequestDate(new \DateTime());
                $user->setSecurityHash(sha1(uniqid(mt_rand(), true)));
                $userManager->update($user);
                $userManager->sendResetMassage($user);
                $this->get("session")->getFlashBag()->add("success",'Na podany adres e-mail został wysłany link resetujący hasło.');
            }

        }
        return $this->render('AppUserBundle:Security:resetPassword.html.twig',array(
            'last_username' => $request->get('email'),
            'error'=>$error,
        ));
    }
    /**
     * Show change password form
     *
     * @param Request $request request
     * @return Response
    */
    public function newPasswordAction(Request $request){
        $userManager = $this->get('user.manager');
        $user = $userManager->findBySecurityHash($request->get('securityHash'));

        if(null===$user){
            $error = 'Link resetujący hasło stracił ważność.';

            return $this->render('AppUserBundle:Security:resetPassword.html.twig',array(
                'last_username' => '',
                'error'=>$error,
            ));
        }

        $form = $this->createForm(new ResetPasswordType(), $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $userManager->changePassword($user);

          $this->get("session")->getFlashBag()->add("success",'Hasło zostało zmienione.');
          return $this->redirect($this->generateUrl('login'));
        }
        return $this->render('AppUserBundle:Security:newPassword.html.twig',array('form'=>$form->createView()));

    }
    /**
     * Register buisness client
     * 
     * @return Response|RedirectResponse
    */
    public function registrationBuisnessClientAction(){
        $request = $this->getRequest();
        $user = new User();
        $userManager = $this->get('user.manager');
        $user->setRoles(array('ROLE_BUISNESS'));

        $form = $this->createForm(new RegistrationType(),$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $userManager->add($user);
            $token = new UsernamePasswordToken($user, $user->getPassword(), "secured_area", $user->getRoles());
            $this->get("security.token_storage")->setToken($token);
            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

            return $this->redirect($this->generateUrl('user_account_start'));
        }
        return $this->render('AppUserBundle:Security:registrationBuisnessClient.html.twig',array('form'=>$form->createView()));
    }
}