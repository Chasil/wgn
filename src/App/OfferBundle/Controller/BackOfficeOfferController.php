<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\OfferBundle\Entity\Offer;
use App\OfferBundle\Form\BackOfficeOfferType;
use App\PaymentBundle\Entity\Payment;
use App\OfferBundle\Model\BackOfficeSearch;
use App\OfferBundle\Form\BackOfficeSearchType;
use App\OfferBundle\Form\BackOfficeOfferOptionsType;

/**
 * Class BackOfficeOfferController
 *
 * @author wojciech przygoda
 */
class BackOfficeOfferController extends Controller
{
    /**
     * Add Offer
     *
    */
    public function addAction() {
        $request = $this->getRequest();
        $offerManager = $this->get('offer.manager');
        $userManager = $this->get('user.manager');
        $user = $userManager->getCurrentLogged();

        if($request->get('id')){
           $offer = $offerManager->findById($request->get('id'));
           $category = $offer->getCategory();
        }else {
            $category = $offerManager->findCategoryById($request->get('category'));
            $offer = new Offer();
            $offer->setCategory($category)
                  ->setUser($user);
        }
         $transactionType = $offerManager->findTransactionTypeById($request->get('transaction'));

        $form = $this->createForm(new BackOfficeOfferType($category,$transactionType),$offer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $offerManager->add($offer);
            $this->get('session')->set('newIdOffer',$offer->getId());

            return $this->redirect($this->generateUrl('backoffice_offer_preview'));
        }

        return $this->render('AppOfferBundle:BackOfficeOffer:add.html.twig',array(
            'offer'=>$offer,
            'form'=>$form->createView(),
            'transaction'=>$transactionType,
            'category'=>$category));
    }
    /**
     * Preview Offer
     *
    */
    public function previewAction(){
        $offerManager = $this->get('offer.manager');
        $offer = $offerManager->findById($this->get('session')->get('newIdOffer'));
        if($this->getRequest()->isMethod('POST')){
            $expireDate = new \DateTime();
            $promoExpire = new \DateTime();
            $offer->setIsPublish(true)
                  ->setExpireDate($expireDate->modify('+'.$offer->getDays().' days'))
                  ->setPromoExpire($promoExpire->modify('+'.$offer->getPromoDays().' days'))
                  ->setPaymentState(Payment::STATE_SUCCESS);
            $offerManager->save($offer);
            $this->addFlash('success','Zapisano poprawnie.');
            return $this->redirect($this->generateUrl('backoffice_offer_list'));
        }

        return $this->render('AppOfferBundle:BackOfficeOffer:preview.html.twig',array(
            'archivOffer'=>($offer->getExpireDate() > new \DateTime()) ? true : false,
            'offer'=>$offer,
            'currencies'=>$offerManager->getCurrencies(),
        ));
    }
    /**
     * List of offers
     *
    */
    public function listAction(){
        $offerManager = $this->get('offer.manager');
        $backOfficeSearch = new BackOfficeSearch();
        $form = $this->createForm(new BackOfficeSearchType(),$backOfficeSearch);
        $form->handleRequest($this->getRequest());

        if($this->getRequest()->query->get('export')=='csv'){
            return $this->get('offer.report_manager')
                        ->generateCsvResponse($backOfficeSearch);
        }

        if($this->getRequest()->query->get('export')=='pdf'){
             return $this->get('offer.report_manager')
                         ->generatePdfResponse($backOfficeSearch);
        }

        return $this->render('AppOfferBundle:BackOfficeOffer:list.html.twig',
                             array('pagination'=>$offerManager->getWidthPagination($backOfficeSearch),
                                   'form'=>$form->createView()));
    }
    /**
     * List of offers in modal View
     *
    */
    public function modalListAction(){
        $offerManager = $this->get('offer.manager');
        $backOfficeSearch = new BackOfficeSearch();
        $form = $this->createForm(new BackOfficeSearchType(),$backOfficeSearch);
        $form->handleRequest($this->getRequest());

        if($this->getRequest()->query->get('export')=='csv'){
            return $this->get('offer.report_manager')
                        ->generateCsvResponse($backOfficeSearch);
        }

        if($this->getRequest()->query->get('export')=='pdf'){
             return $this->get('offer.report_manager')
                         ->generatePdfResponse($backOfficeSearch);
        }

        return $this->render('AppOfferBundle:BackOfficeOffer:listModal.html.twig',
                             array('pagination'=>$offerManager->getWidthPagination($backOfficeSearch),
                                   'form'=>$form->createView()));
    }
    /**
     * Delete offer
     *
    */
    public function deleteAction() {
        $offerManager = $this->get('offer.manager');
        $offer = $offerManager->findById($this->getRequest()->get('id'));
        $this->denyAccessUnlessGranted('delete', $offer);
        $offerManager->softDelete($offer);

        $this->addFlash('success','Usunięto poprawnie.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Change offer publication state
     *
    */
    public function changePublishAction() {
        $offerManager = $this->get('offer.manager');
        $offerManager->changePublish($this->getRequest()->get('id'),
                                     $this->getRequest()->get('publish'));

        $this->addFlash('success','Zapisano poprawnie.');

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    /**
     * Edit offer
     *
    */
    public function editAction(){
        $request = $this->getRequest();
        $offerManager = $this->get('offer.manager');
        $offer = $offerManager->findById($request->get('id'));
        $category = $offer->getCategory();
        $transactionType = $offer->getTransactionType();

        $form = $this->createForm(new BackOfficeOfferType($category,$transactionType),$offer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $offerManager->add($offer);
            $this->get('session')->set('newIdOffer',$offer->getId());
            return $this->redirect($this->generateUrl('backoffice_offer_preview'));
        }

        return $this->render('AppOfferBundle:BackOfficeOffer:add.html.twig',array(
            'offer'=>$offer,
            'form'=>$form->createView(),
            'category'=>$category,
            'transaction'=>$transactionType,));
    }
    /**
     * Change offer options
     *
    */
    public function changeOptionsAction(){
        $offerManager = $this->get('offer.manager');
        $offer = $offerManager->findById($this->getRequest()->get('id'));
        $this->denyAccessUnlessGranted('edit', $offer);
        $form = $this->createForm(new BackOfficeOfferOptionsType(),$offer);
        $form->handleRequest($this->getRequest());

        if($form->isSubmitted() && $form->isValid()){
            $expireDate = new \DateTime();
            $promoExpire = new \DateTime();
            $offer->setExpireDate($expireDate->modify('+'.$offer->getDays().' days'))
                  ->setPromoExpire($promoExpire->modify('+'.$offer->getPromoDays().' days'))
                  ->setPaymentState(Payment::STATE_SUCCESS);
            $offerManager->save($offer);

            $this->addFlash('success','Zapisano poprawnie.');
            return $this->redirect($this->generateUrl('backoffice_offer_list'));
        }
        return $this->render('AppOfferBundle:BackOfficeOffer:changeOptions.html.twig',
                             array('form'=>$form->createView()));
    }
}
