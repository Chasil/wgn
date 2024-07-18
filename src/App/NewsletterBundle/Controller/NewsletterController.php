<?php
/**
 * This file is part of the AppNewsletterBundle package.
 *
 */
namespace App\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\NewsletterBundle\Model\SearchEmails;
use App\NewsletterBundle\Form\SearchEmailsType;
use App\NewsletterBundle\Entity\Newsletter;
use App\NewsletterBundle\Form\NewsletterType;
use App\NewsletterBundle\Form\MessageType;
use App\NewsletterBundle\Model\Message;

/**
 * Class NewsletterController
 *
 * @author wojciech przygoda
 */
class NewsletterController extends Controller
{
    /**
     * List of newsletter subscriptions
     *
    */
    public function listAction()
    {
        $newsletterManager = $this->get('newsletter.manager');
        $searchEmails = new SearchEmails();
        $form = $this->createForm(new SearchEmailsType(),$searchEmails);
        $form->handleRequest($this->getRequest());

        return $this->render('AppNewsletterBundle:Newsletter:list.html.twig',
                             array('pagination'=>$newsletterManager->getWidthPagination($searchEmails),
                                   'form'=>$form->createView()));
    }
    public function listSentMessagesAction()
    {
        $newsletterManager = $this->get('newsletter.manager');


        return $this->render('AppNewsletterBundle:Newsletter:listSentMessages.html.twig',
                             array('pagination'=>$newsletterManager->getSentMessages(),
                                   ));
    }
    /**
     * Send newsletter
     *
    */
    public function sendAction()
    {
        $newsletterManager = $this->get('newsletter.manager');
        $message = new Message();
        $form = $this->createForm(new MessageType(),$message);
        $form->handleRequest($this->getRequest());

        if($form->isSubmitted() && $form->isValid()){
            $newsletterManager->send($message);
            $this->get("session")->getFlashBag()->set("success",  'Wysłano poprawnie.');
            return $this->redirect($this->generateUrl('backoffice_newsletter_list'));
        }
        return $this->render('AppNewsletterBundle:Newsletter:send.html.twig',array('form'=>$form->createView()));
    }
    /**
     * Add subscription to newsletter
     *
    */
    public function addAction()
    {
        $newsletterManager = $this->get('newsletter.manager');
        $newsletter = new Newsletter();
        $form = $this->createForm(new NewsletterType(),$newsletter);
        $form->handleRequest($this->getRequest());

        if($form->isSubmitted() && $form->isValid()){
            $newsletterManager->add($newsletter);

            $this->get("session")->getFlashBag()->set("success",  'Zapisano poprawnie.');
        }
        return $this->render('AppNewsletterBundle:Newsletter:add.html.twig',array(
            'form'=>$form->createView(),
            ));
    }
    /**
     * Delete subscription from newsletter
     *
    */
    public function deleteAction()
    {
        $newsletterManager = $this->get('newsletter.manager');
        $newsletterManager->remove($this->getRequest()->get('id'));

        $this->get("session")->getFlashBag()->set("success",  'Zapisano poprawnie.');
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Activate subscription
     *
    */
    public function activeAction() {
        $id = $this->getRequest()->get('id');
        $enabled = $this->getRequest()->get('active');

        $newsletterManager = $this->get('newsletter.manager');
        $newsletterManager->changeActive($id,$enabled);

        $this->get("session")->getFlashBag()->set("success",  'Zapisano poprawnie.');
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Confirm subscription
     *
    */
    public function confirmAction() {

        $hash = $this->getRequest()->get('code');

        $newsletterManager = $this->get('newsletter.manager');
        $newsletterManager->activateByHash($hash);

        $this->get("session")->getFlashBag()->set("success",  'Twoj adres został dodany.');
        return $this->redirect($this->generateUrl('frontend_newsletter_add'));
    }
    /**
     * Deactivate subscription
     *
    */
    public function deactivateAction() {

        $hash = $this->getRequest()->get('code');

        $newsletterManager = $this->get('newsletter.manager');
        $newsletterManager->deactivateByHash($hash);

        $this->get("session")->getFlashBag()->set("success",  'Twoj adres został wypisany.');
        return $this->redirect($this->generateUrl('frontend_newsletter_add'));
    }
    /**
     * Deactivate looking for subscription
     *
    */
    public function deactivateLookingForAction() {

        $hash = $this->getRequest()->get('code');

        $newsletterManager = $this->get('newsletter.manager');
        $newsletterManager->deactivateLookingForByHash($hash);

        $this->get("session")->getFlashBag()->set("success",  'Twoj adres został wypisany.');
        return $this->redirect($this->generateUrl('subscription_offers'));
    }
}
