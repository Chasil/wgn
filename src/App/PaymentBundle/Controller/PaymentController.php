<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\PaymentBundle\Form\SearchPaymentsType;
use App\PaymentBundle\Model\SearchPayments;

/**
 * Class PaymentController
 *
 * @author wojciech przygoda
 */
class PaymentController extends Controller
{
    /**
     * List of payments
     *
    */
    public function listAction()
    {
        $searchPayments = new SearchPayments();
        $paymentManager = $this->get('payment.manager');
        $form = $this->createForm(new SearchPaymentsType(), $searchPayments);

        $form->handleRequest($this->getRequest());

        if($this->getRequest()->query->get('export')=='csv'){
            return $this->get('payment.report_manager')
                        ->generateCsvResponse($searchPayments);
        }

        if($this->getRequest()->query->get('export')=='pdf'){
             return $this->get('payment.report_manager')
                         ->generatePdfResponse($searchPayments);
        }
        return $this->render('AppPaymentBundle:Payment:list.html.twig',array(
                                'pagination'=>$paymentManager->getWidthPagination($searchPayments),
                                   'form'=>$form->createView()));
    }
    /**
     * Show payment details in modal
     *
    */
    public function detailsAction(){
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()){
            $paymentManager = $this->get('payment.manager');
            $payment = $paymentManager->getDetails($request->get('id'));
            return $this->render('AppPaymentBundle:Payment:details.html.twig',array('payment'=>$payment));
        }

        throw $this->createAccessDeniedException('You cannot access this page!');
    }
}
