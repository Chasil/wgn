<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\UserBundle\Entity\User;
use App\UserBundle\Form\UserType;
use App\UserBundle\Form\SearchUsersType;
use App\UserBundle\Model\SearchUsers;
use App\UserBundle\Model\SearchOfficeManagers;
use App\UserBundle\Form\SearchOfficeManagersType;
use App\UserBundle\Model\SearchClients;
use App\UserBundle\Form\SearchClientsType;
use App\UserBundle\Model\Contact;
use App\UserBundle\Form\ContactType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\UserBundle\Form\ResetPasswordType;
use App\UserBundle\Form\ChangeEmailType;
use App\UserBundle\Form\OrderType;
use App\UserBundle\Model\Order;
use App\UserBundle\Form\UserAddressType;
use App\UserBundle\Form\CompanyDataType;
use App\UserBundle\Entity\Address;
use App\UserBundle\Entity\CompanyData;
use App\UserBundle\Form\AvatarType;

/**
 * Class UserController
 *
 * @author wojciech przygoda
 */
class UserController extends Controller {
    /**
     * List of users
     * @return Response
    */
    public function listAction() {
        $userManager = $this->get('user.manager');
        $searchUsers = new SearchUsers();
        $form = $this->createForm(new SearchUsersType(),$searchUsers);
        $form->handleRequest($this->getRequest());

        if($this->getRequest()->query->get('export')=='csv'){
            return $this->get('user.report_manager')
                        ->generateCsvResponse($searchUsers);
        }

        if($this->getRequest()->query->get('export')=='pdf'){
             return $this->get('user.report_manager')
                         ->generatePdfResponse($searchUsers);
        }

        return $this->render('AppUserBundle:User:userList.html.twig',
                             array('users'=>$userManager->getWidthPagination($searchUsers),
                                   'form'=>$form->createView()));
    }
    /**
     * List of office managers
     * @return Response
    */
    public function officeManagersListAction() {
        $userManager = $this->get('user.manager');
        $searchOfficeManagers = new SearchOfficeManagers();
        $form = $this->createForm(new SearchOfficeManagersType(),$searchOfficeManagers);
        $form->handleRequest($this->getRequest());

        if($this->getRequest()->query->get('export')=='csv'){
            return $this->get('user.report_manager')
                        ->generateCsvResponse($searchOfficeManagers);
        }

        if($this->getRequest()->query->get('export')=='pdf'){
             return $this->get('user.report_manager')
                         ->generatePdfResponse($searchOfficeManagers);
        }

        return $this->render('AppUserBundle:User:officeManagerList.html.twig',
                             array('users'=>$userManager->getWidthPagination($searchOfficeManagers),
                                   'form'=>$form->createView()));
    }
    /**
     * List of clients
     * @return Response
    */
    public function clientsListAction() {
        $userManager = $this->get('user.manager');
        $searchClients = new SearchClients();
        $form = $this->createForm(new SearchClientsType(),$searchClients);
        $form->handleRequest($this->getRequest());

        if($this->getRequest()->query->get('export')=='csv'){
            return $this->get('user.report_manager')
                        ->generateCsvResponse($searchClients);
        }

        if($this->getRequest()->query->get('export')=='pdf'){
             return $this->get('user.report_manager')
                         ->generatePdfResponse($searchClients);
        }

        return $this->render('AppUserBundle:User:clientList.html.twig',
                             array('users'=>$userManager->getWidthPagination($searchClients),
                                   'form'=>$form->createView()));
    }
    /**
     * Add user
     * @return Response|RedirectResponse
    */
    public function addAction()
    {
        $request = $this->getRequest();
        $userManager = $this->get('user.manager');
        $type = $request->get('type');
        $user = new User();
        $form = $this->createForm(new UserType($type,$userManager), $user);

        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->add($user);

            $params = array();

            if($type=='agent'){
                $type = 'sortable_agent';
                $params = array('idOffice'=>$user->getOffice()->getId());
            }
            $this->get("session")->getFlashBag()->set("success",  'Zapisano Poprawnie.');
            return $this->redirect($this->generateUrl('backoffice_'.$type.'_list',$params));
        }

        return $this->render('AppUserBundle:User:add' . ucfirst($type) . '.html.twig',
                             array('form'=>$form->createView(),
                                   'user'=>$user));
    }
    /**
     * Edit user
     * @return Response|RedirectResponse
    */
    public function editAction()
    {
        $request = $this->getRequest();
        $userManager = $this->get('user.manager');

        $user = $userManager->findById($request->get('id'));
        $type = $user->getType();
        if(!$user->getDefaultAddress()){
            $user->getAddresses()->add(new Address());
        }
        $form = $this->createForm(new UserType($type,$userManager), $user);
        $form->remove('plainPassword');
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {

            $params = array();

            if($type=='agent'){
                $type = 'sortable_agent';
                $params = array('idOffice'=>$user->getOffice()->getId());
            }

            $userManager->save($user);

            $this->get("session")->getFlashBag()->set("success",  'Zapisano Poprawnie.');
            return $this->redirect($this->generateUrl('backoffice_'.$type.'_list',$params));
        }

        return $this->render('AppUserBundle:User:add' . ucfirst($type) . '.html.twig',
                             array('form'=>$form->createView(),
                                   'user'=>$user));
    }
    /**
     * Change user locale
     *
     * @param Request $request request
     * @return RedirectResponse
    */
    public function changeLocaleAction(Request $request){
        if($request->headers->get('referer')){
           return $this->redirect($this->generateUrl('front_page'));
        }else {
           return $this->redirect($this->generateUrl('front_page'));
        }
    }
    /**
     * Show user account
     *
     * @return Response
    */
    public function accountAction() {
        $offerManager = $this->get('offer.manager');
        $offers = $offerManager->getByUserWidthPagination();
        $form = $this->createForm(new ContactType($this->getDoctrine()->getManager()),new Contact());
        return $this->render('AppUserBundle:User:account.html.twig',
                             array('offers'=>$offers,
                                   'form'=>$form->createView()));
    }
    /**
     * Show user account start page
     *
    */
    public function accountStartAction() {
        $form = $this->createForm(new ContactType($this->getDoctrine()->getManager()),new Contact());
        return $this->render('AppUserBundle:User:accountStart.html.twig',
                             array('form'=>$form->createView()));
    }
    /**
     * Show user observed offers
     *
     * @return Response
    */
    public function observedAction() {
        $observedManager = $this->get('observed.manager');
        $offers = $observedManager->getByUserWidthPagination();
        $form = $this->createForm(new ContactType($this->getDoctrine()->getManager()),new Contact());
        return $this->render('AppUserBundle:User:observed.html.twig',
                             array('offers'=>$offers,
                                   'form'=>$form->createView()));
    }
    /**
     * Show user messages
     *
     * @return Response
    */
    public function messagesAction() {
        $messageManager = $this->get('message.manager');
        $form = $this->createForm(new ContactType($this->getDoctrine()->getManager()),new Contact());
        $messages = $messageManager->getByUserWidthPagination();

        return $this->render('AppUserBundle:User:messages.html.twig',
                             array('messages'=>$messages,
                                   'form'=>$form->createView()
                ));
    }
    /**
     * Order subscription
     *
     * @return Response
    */
    public function orderAction() {
        $userManager = $this->get('user.manager');
        $order = new Order();
        $user = $userManager->getCurrentLogged();
        $companyData = $user->getCompanyData();

        $order->setLogin($user->getUsername())
              ->setContactPerson($user->getFirstName().' '.$user->getLastName())
              ->setPackage(50);
        if($companyData){
            $order->setNip($companyData->getNip());
        }

        $form = $this->createForm(new OrderType(),$order);
        $form->handleRequest($this->getRequest());

        if($form->isSubmitted() && $form->isValid()){
            $userManager->sendOrder($order);
            $this->get("session")->getFlashBag()->set("success",  'Wysłano zamówienie.');
        }
        return $this->render('AppUserBundle:User:order.html.twig',
                             array('form'=>$form->createView()
                ));
    }
    /**
     * Show user payments
     *
     * @return Response
    */
    public function paymentsAction() {

        $form = $this->createForm(new ContactType($this->getDoctrine()->getManager()),new Contact());
        $form->handleRequest($this->getRequest());
        $paymentsManager = $this->get('payment.manager');
        $payments = $paymentsManager->getUserWidthPagination();
        return $this->render('AppUserBundle:User:payments.html.twig',
                             array('payments'=>$payments,
                                   'form'=>$form->createView()));
    }
    /**
     * Show user settings
     *
     * @return Response
    */
    public function settingsAction() {
        $userManager = $this->get('user.manager');
        $user = $userManager->getCurrentLogged();
        $passwordForm = $this->createForm(new ResetPasswordType(), $user);
        $passwordForm->handleRequest($this->getRequest());

        $emailForm = $this->createForm(new ChangeEmailType(), $user);
        $emailForm->handleRequest($this->getRequest());
        $address = $user->getDefaultAddress();

        if(!is_object($address)){
            $address = new Address();
            $address->setUser($user);
        }

        $addressForm = $this->createForm(new UserAddressType(), $address);
        $addressForm->handleRequest($this->getRequest());
        $companyData = $user->getCompanyData();

        if(!is_object($companyData)){
            $companyData = new CompanyData();
        }

        $companyDataForm = $this->createForm(new CompanyDataType(), $companyData);
        $companyDataForm->handleRequest($this->getRequest());

        $avatarForm = $this->createForm(new AvatarType(), $user);


        if($passwordForm->isSubmitted() && $passwordForm->isValid()){
            $userManager->add($user);
            $this->get("session")->getFlashBag()->set("success",  'Zapisano Poprawnie.');
        }
        if($emailForm->isSubmitted() && $emailForm->isValid()){
            $userManager->save($user);
            $this->get("session")->getFlashBag()->set("success",  'Zapisano Poprawnie.');
        }

        if($addressForm->isSubmitted() && $addressForm->isValid()){
            $userManager->save($address);
            $this->get("session")->getFlashBag()->set("success",  'Zapisano Poprawnie.');
        }
        if($companyDataForm->isSubmitted() && $companyDataForm->isValid()){
            $user->setCompanyData($companyData);
            $userManager->save($user);
            $this->get("session")->getFlashBag()->set("success",  'Zapisano Poprawnie.');
        }

        return $this->render('AppUserBundle:User:settings.html.twig',
                             array('passwordForm'=>$passwordForm->createView(),
                                   'emailForm'=>$emailForm->createView(),
                                   'addressForm'=>$addressForm->createView(),
                                   'compnayDataForm'=>$companyDataForm->createView(),
                                   'avatarForm'=>$avatarForm->createView(),
                                   'user'=>$user
                ));
    }
    /**
     * Enable or disable user
     *
     * @return RedirectResponse
    */
    public function enabledAction() {
        $id = $this->getRequest()->get('id');
        $enabled = $this->getRequest()->get('enabled');

        $userManager = $this->get('user.manager');
        $userManager->changeEnabled($id,$enabled );

        $this->get("session")->getFlashBag()->set("success",  'Zapisano Poprawnie.');
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Enable or disable office management
     *
     * @return RedirectResponse
    */
    public function enableOfficeManagementAction() {
        $id = $this->getRequest()->get('id');
        $enabled = $this->getRequest()->get('enabled');

        $userManager = $this->get('user.manager');
        $userManager->changeofficeManagement($id,$enabled );

        $this->get("session")->getFlashBag()->set("success",  'Zapisano Poprawnie.');
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Delete user
     *
     * @return RedirectResponse
    */
    public function deleteAction(){
        $userManager = $this->get('user.manager');
        $userManager->delete($this->getRequest()->get('id'));
        $this->addFlash('success','Usunięto poprawnie.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Send message form user account form
     *
     * @return JsonResponse
    */
    public function sendMessageAction(){
        $request =  $this->getRequest();
        $contactData = new Contact();
        $form = $this->createForm(new ContactType($this->getDoctrine()->getManager()),
                                  $contactData);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $mailer = $this->get('mailer');
            $message = $mailer->createMessage()
                ->setSubject($contactData->getSubject())
                ->setFrom(array($this->getParameter('mail_sender_email')=>$this->getParameter('mail_sender_name')))
                ->setTo($this->getParameter('mail_admin_email'))
                ->setBody(
                    $this->renderView(
                        'AppUserBundle:Emails:ask.html.twig',
                        array('contactData'=>$contactData)
                    ),
                    'text/html'
                )
            ;

            $mailer->send($message);
            return new JsonResponse(array('success'=>true));
        }

        throw $this->createAccessDeniedException('You cannot access this page!');
    }
    /**
     * Show message details
     *
     * @return JsonResponse
    */
    public function messageDetailsAction(){
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()){
            $messageManager = $this->get('message.manager');
            $message = $messageManager->getDetails($request->get('id'));
            return $this->render('AppUserBundle:User:messageDetails.html.twig',array('message'=>$message));
        }

        throw $this->createAccessDeniedException('You cannot access this page!');
    }

    /**
     * Change user position on list
     *
     * @param int $id user id
     * @param string $direction change ordering direction
     * @return RedirectResponse
     */
    public function changeOrderingAction($id,$direction)
    {
        $userManager = $this->get('user.manager');
        $userManager->changeOrdering($id,$direction);

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
}
