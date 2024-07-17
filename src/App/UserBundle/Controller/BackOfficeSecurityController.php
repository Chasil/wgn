<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\UserBundle\Form\ResetPasswordType;
use App\UserBundle\Form\ChangePasswordType;

/**
 * Class BackOfficeSecurityController
 *
 * @author wojciech przygoda
 */
class BackOfficeSecurityController extends Controller
{
    /**
     * Login user
     * @return Response
    */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'AppUserBundle:BackOfficeSecurity:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
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
            }
            if($user->isPasswordRequestNonExpired(60*60*24)){
                $error = 'Hasło było resetowane w ciągu 24h.';
            }else {
                $user->setPasswordRequestDate(new \DateTime());
                $user->setSecurityHash(sha1(uniqid(mt_rand(), true)));
                $userManager->update($user);
                $userManager->sendResetMassage($user,true);
                $this->get("session")->getFlashBag()->add("success",'Na podany adres e-mail został wysłany link resetujący hasło.');
            }

        }
        return $this->render('AppUserBundle:BackOfficeSecurity:resetPassword.html.twig',array(
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

            return $this->render('AppUserBundle:BackOfficeSecurity:resetPassword.html.twig',array(
                'last_username' => '',
                'error'=>$error,
            ));
        }

        $form = $this->createForm(new ResetPasswordType(), $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $userManager->changePassword($user);

          $this->get("session")->getFlashBag()->add("success",'Hasło zostało zmienione.');
          return $this->redirect($this->generateUrl('backoffice_login'));
        }
        return $this->render('AppUserBundle:BackOfficeSecurity:newPassword.html.twig',array('form'=>$form->createView()));

    }
    /**
     * Change user password
     *
     * @return Response|RedirectResponse
    */
    public function changePasswordAction(){
        $request = $this->getRequest();
        $userManager = $this->get('user.manager');

        $user = $userManager->findById($request->get('id'));
        $type = $user->getType();

        $form = $this->createForm(new ChangePasswordType(), $user);
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {


            $userManager->changePassword($user);

            $this->get("session")->getFlashBag()->set("success",  'Zapisano Poprawnie.');
            return $this->redirect($this->generateUrl('backoffice_'.$type.'_list'));
        }

        return $this->render('AppUserBundle:BackOfficeSecurity:changePassword.html.twig',
                             array('form'=>$form->createView(),
                                   'user'=>$user));
    }
}