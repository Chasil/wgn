<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\OfficeBundle\Model\SearchOffices;
use App\OfficeBundle\Form\SearchOfficesType;
use App\OfficeBundle\Model\Contact;
use App\OfficeBundle\Form\ContactType;
use App\OfficeBundle\Form\OfficeType;
use App\OfficeBundle\Entity\Office;
use App\UserBundle\Entity\Address;
use App\OfficeBundle\Form\SearchAgentsType;
use App\OfficeBundle\Model\SearchAgents;
/**
 * Class OfficeController
 *
 * @author wojciech przygoda
 */
class OfficeController extends Controller {
    /**
     * List of offices
     *
    */
    public function listAction() {

        $officeManager = $this->get('office.manager');
        $searchOffices = new SearchOffices();
        $form = $this->createForm(new SearchOfficesType(), $searchOffices);
        $form->handleRequest($this->getRequest());

        if ($this->getRequest()->query->get('export') == 'csv') {
            return $this->get('office.report_manager')
                            ->generateCsvResponse($searchOffices);
        }

        if ($this->getRequest()->query->get('export') == 'pdf') {
            return $this->get('office.report_manager')
                            ->generatePdfResponse($searchOffices);
        }

        return $this->render('AppOfficeBundle:Office:list.html.twig', array(
                    'pagination' => $officeManager->getWidthPagination($searchOffices),
                    'form' => $form->createView()
        ));
    }
    /**
     * Show office
     *
    */
    public function showAction() {
        $request = $this->getRequest();
        $officeManager = $this->get('office.manager');
        $offerManager = $this->get('offer.manager');
        $linkManager = $this->get('office.link.manager');

        if($request->attributes->has('subdomain')){
            $office = $officeManager->findBySubdomain($request->get('subdomain'),true);
        }else {
            $office = $officeManager->findById($request->get('id'),true);
            return $this->redirect($this->getParameter('scheme') . '://' . $office->getSubdomain() . '.'. $this->getParameter('domain'));
        }

        if(is_null($office)){
            return $this->redirect($this->getParameter('main_host'));
        }

        $contact = new Contact();
        $contact->setOffice($office);

        $form = $this->createForm(new ContactType($this->getDoctrine()->getManager()), $contact);

        return $this->render('AppOfficeBundle:Office:show.html.twig', array(
                    'city' => $request->get('subdomain'),
                    'office' => $office,
                    'form' => $form->createView(),
                    'sepcialOffers' => $offerManager->getSpecialByOfficeId($office->getId(), 100),
                    'links'=>$linkManager->getAllByOffice($office),
        ));
    }
    /**
     * Show agent modal
     *
    */
    public function getAgentAction() {
        $request = $this->getRequest();
        $userManager = $this->get('user.manager');

        if ($request->isXmlHttpRequest()) {
            $agent = $userManager->findAgentById($request->get('id'));
            return $this->render('AppOfficeBundle:Office:agent.html.twig', array('agent' => $agent));
        }

        throw $this->createAccessDeniedException('You cannot access this page!');
    }
    /**
     * List of office agents
     *
    */
    public function agentsListAction() {
        $userManager = $this->get('user.manager');
        $searchAgents = new SearchAgents();
        $form = $this->createForm(new SearchAgentsType(),$searchAgents);
        $form->handleRequest($this->getRequest());

        if($this->getRequest()->query->get('export')=='csv'){
            return $this->get('user.report_manager')
                        ->generateCsvResponse($searchAgents);
        }

        if($this->getRequest()->query->get('export')=='pdf'){
             return $this->get('user.report_manager')
                         ->generatePdfResponse($searchAgents);
        }

        return $this->render('AppOfficeBundle:Office:agentList.html.twig',
                             array('users'=>$userManager->getWidthPagination($searchAgents),
                                   'form'=>$form->createView()));
    }
    /**
     * Sortable List of office agents
     *
    */
    public function sortableAgentsListAction() {
        $userManager = $this->get('user.manager');
        $searchAgents = new SearchAgents();
        $form = $this->createForm(new SearchAgentsType(),$searchAgents);
        $form->handleRequest($this->getRequest());

        if($this->getRequest()->query->get('export')=='csv'){
            return $this->get('user.report_manager')
                        ->generateCsvResponse($searchAgents);
        }

        if($this->getRequest()->query->get('export')=='pdf'){
             return $this->get('user.report_manager')
                         ->generatePdfResponse($searchAgents);
        }

        return $this->render('AppOfficeBundle:Office:sortableAgentList.html.twig',
                             array('users'=>$userManager->getWidthPagination($searchAgents),
                                   'form'=>$form->createView()));
    }
    /**
     * Send message form office form
     *
    */
    public function sendMessageAction() {
        $request = $this->getRequest();
        $contactData = new Contact();
        $form = $this->createForm(new ContactType($this->getDoctrine()->getManager()), $contactData);
        $form->handleRequest($request);
        if ($request->isXmlHttpRequest() &&
                $form->isSubmitted() && $form->isValid()) {


            $mailer = $this->get('mailer');
            $message = $mailer->createMessage()
                    ->setSubject($contactData->getSubject())
                    ->setFrom(array($this->getParameter('mail_sender_email') => $this->getParameter('mail_sender_name')))
                    ->setTo($contactData->getOffice()->getEmail())
                    ->setBody(
                    $this->renderView(
                            'AppOfficeBundle:Emails:ask.html.twig', array('contactData' => $contactData)
                    ), 'text/html'
                    )
            ;

            $mailer->send($message);
            return new JsonResponse(array('success' => true));
        }
        throw $this->createAccessDeniedException('You cannot access this page!');
    }
    /**
     * Search office
     *
    */
    public function searchAction(){
        $officeManager = $this->get('office.manager');
        $results = $officeManager->search($this->getRequest()->get('q'));
        return new JsonResponse(array('success' => true,'results'=>$results));
    }
    /**
     * Edit office
     *
    */
    public function editAction() {
        $request = $this->getRequest();
        $officeManager = $this->get('office.manager');
        $office = $officeManager->findById($request->get('id'));

        $this->denyAccessUnlessGranted('manage', $office);

        $form = $this->createForm(new OfficeType(), $office);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $officeManager->save($office);

            $this->addFlash('success','Zapisano poprawnie.');

            if($this->get('security.authorization_checker')->isGranted('ROLE_MANAGER')) {
                return $this->redirect($this->generateUrl('backoffice_offices_list'));
            }else {
                return $this->redirect($this->generateUrl('backoffice_my_office'));
            }
        }
        return $this->render('AppOfficeBundle:Office:edit.html.twig', array(
                    'form' => $form->createView(),
                    'office'=>$office,
        ));
    }
    /**
     * Add office
     *
    */
    public function addAction() {
        $request = $this->getRequest();
        $officeManager = $this->get('office.manager');
        $office = new Office();

        $this->denyAccessUnlessGranted('manage', $office);

        $address = new Address();
        $office->addAddress($address);
        $form = $this->createForm(new OfficeType(), $office);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $officeManager->save($office);

            $this->addFlash('success','Zapisano poprawnie.');

        }
        return $this->render('AppOfficeBundle:Office:edit.html.twig', array(
                    'form' => $form->createView(),
                    'office'=>$office,
        ));
    }
    /**
     * Change office publication state
     *
    */
    public function changePublishAction()
    {
        $id = $this->getRequest()->get('id');
        $publish = $this->getRequest()->get('publish');

        $officeManager = $this->get('office.manager');
        $officeManager->changePublish($id,$publish );

        $this->addFlash('success','Pozycja została zmieniona.');

        return $this->redirect($this->getRequest()->headers->get('referer'));

    }
    /**
     * Show agent office
     *
    */
    public function showMyAction(){
        $userManager = $this->get('user.manager');
        $user = $userManager->getCurrentLogged();

        $office = $user->getOffice();

        $this->denyAccessUnlessGranted('manage', $office);

        return $this->render('AppOfficeBundle:Office:showMy.html.twig', array(
                    'office' => $office,
        ));
    }
}
