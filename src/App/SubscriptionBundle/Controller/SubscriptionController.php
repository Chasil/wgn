<?php
/**
 * This file is part of the AppSubscriptionBundle package.
 *
 */
namespace App\SubscriptionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\SubscriptionBundle\Form\SearchSubscriptionsType;
use App\SubscriptionBundle\Model\SearchSubscriptions;
use App\SubscriptionBundle\Entity\Subscription;
use App\SubscriptionBundle\Form\SubscriptionType;

/**
 * Class SubscriptionController
 *
 * @author wojciech przygoda
 */
class SubscriptionController extends Controller
{
    /**
     * List of subscriptions
     *
    */
    public function listAction()
    {
        $subscriptionManager = $this->get('subscription.manager');
        $searchSubscriptions = new SearchSubscriptions();
        $form = $this->createForm(new SearchSubscriptionsType(),$searchSubscriptions);
        $form->handleRequest($this->getRequest());

        if($this->getRequest()->query->get('export')=='csv'){
            return $this->get('subscription.report_manager')
                        ->generateCsvResponse($searchSubscriptions);
        }

        if($this->getRequest()->query->get('export')=='pdf'){
             return $this->get('subscription.report_manager')
                         ->generatePdfResponse($searchSubscriptions);
        }

        return $this->render('AppSubscriptionBundle:Subscription:list.html.twig',
                             array('pagination'=>$subscriptionManager->getWidthPagination($searchSubscriptions),
                                   'form'=>$form->createView()));
    }
    /**
     * Add subscripton
     *
    */
    public function addAction()
    {
        $subscription = new Subscription();
        $form = $this->createForm(new SubscriptionType(), $subscription);

        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {

            $subscriptionManager = $this->get('subscription.manager');
            $subscriptionManager->save($subscription);

            $this->get("session")->getFlashBag()->set("success",  'Zapisano Poprawnie.');
            return $this->redirect($this->generateUrl('backoffice_subscription_list'));
        }

        return $this->render('AppSubscriptionBundle:Subscription:add.html.twig',
                             array('form'=>$form->createView(),
                                   ));
    }
    /**
     * Delete subscripton
     *
    */
    public function deleteAction()
    {
        $id = $this->getRequest()->get('id');
        $subscriptionManager = $this->get('subscription.manager');
        $subscriptionManager->remove($id);
        $this->addFlash('success','Usunięto poprawnie.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Show subscripton details in modal
     *
    */
    public function detailsAction(){
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()){
            $userManager = $this->get('user.manager');
            $user = $userManager->getCurrentLogged();
            $subscription = $user->getActiveSubscription();

            return $this->render('AppSubscriptionBundle:Subscription:details.html.twig',array('subscription'=>$subscription));
        }

        throw $this->createAccessDeniedException('You cannot access this page!');
    }
}
