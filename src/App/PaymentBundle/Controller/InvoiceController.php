<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\PaymentBundle\Form\SearchInvoicesType;
use App\PaymentBundle\Model\SearchInvoices;

/**
 * Class InvoiceController
 *
 * @author wojciech przygoda
 */
class InvoiceController extends Controller
{
    /**
     * List of invoices
     *
    */
    public function listAction()
    {
        $searchInvoices = new SearchInvoices();
        $invoiceManager = $this->get('invoice.manager');
        $form = $this->createForm(new SearchInvoicesType(), $searchInvoices);

        $form->handleRequest($this->getRequest());

        return $this->render('AppPaymentBundle:Invoice:list.html.twig',array(
                                'pagination'=>$invoiceManager->getWidthPagination($searchInvoices),
                                   'form'=>$form->createView()));
    }
    /**
     * Show invoice details in pdf format
     *
    */
    public function detailsAction(){
        $request = $this->getRequest();
        $invoiceManager = $this->get('invoice.manager');
        $invoice = $invoiceManager->findById($request->get('id'));
        $this->denyAccessUnlessGranted('view', $invoice);

        $html = $this->get('templating')->render('AppPaymentBundle:PDF:invoice.html.twig',array(
            'invoice' =>$invoice
            ));
        return $this->get('tfox.mpdfport')->generatePdfResponse($html);
    }
}
