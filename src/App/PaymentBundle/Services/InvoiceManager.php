<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\PaymentBundle\Entity\Payment;
use App\PaymentBundle\Entity\Invoice;
use App\PaymentBundle\Entity\InvoiceItem;
use App\PaymentBundle\Entity\PaymentItem;
use App\PaymentBundle\Model\SearchInvoices;
/**
 * Class InvoiceManager
 *
 * @author wojciech przygoda
 */
class InvoiceManager {
    /**
     *
     * @var Container services container
     */
    private $services;
    /**
     * Constructor
     *
     * @param Container $container services container
     */
     public function __construct(Container $container) {
      $this->services = $container;
    }
    /**
     * create from payment
     *
     * @param Payment $payment payment
     * @param string $type type
     * @return null
     */
    public function createFromPayment(Payment $payment,
                                      $type = Invoice::TYPE_PUBLICATION){
        $user = $payment->getUser();
        $em = $this->services->get('doctrine')->getManager();

        if(!is_object($user)){
            return;
        }
        if(!$user->hasNip()){
            return;
        }

        $prevInvoice = $this->getHighestInThisMonth($type);
        $invoice = new Invoice();
        $invoice->setNumberType($type)
                ->setPayment($payment);
        if(is_object($prevInvoice)){
            $invoice->setCounter($prevInvoice->getCounter()+1);
        }else {
            $invoice->setCounter(1);
        }
        $invoice->prepereNumber();

        $invoice->setUser($user)
                ->setCompanyName($this->services
                                      ->getParameter('invoice_company_name'))
                ->setCity($this->services
                               ->getParameter('invoice_company_city'))
                ->setZipCode($this->services
                                  ->getParameter('invoice_company_zip_code'))
                ->setStreet($this->services
                                 ->getParameter('invoice_company_street'))
                ->setCountry($this->services
                                  ->getParameter('invoice_company_country'))
                ->setNip($this->services
                              ->getParameter('invoice_company_nip'))
                ;

        $compnayData = $user->getCompanyData();
        $address = $compnayData->getAddress();

        if(!is_object($address)){
            return;
        }

        $invoice->setClientCompanyName($compnayData->getName())
                ->setClientCountry($address->getCountry()->getName())
                ->setClientFirstName($user->getFirstName())
                ->setClientLastName($user->getLastName())
                ->setClientCity($address->getCity())
                ->setClientStreet($address->getStreet())
                ->setClientZipCode($address->getZipCode())
                ->setClientNip($compnayData->getNip())
                ;


        foreach($payment->getPaymentItems() as $item){

            $itemName = $this->getItemName($item->getType()).' '.
                        $item->getOffer()->getSignature();

            $invoiceItem = new InvoiceItem();

            $invoiceItem->setQuantity(1)
                        ->setGrossPrice($item->getValue())
                        ->setTax(23)
                        ->setNetprice($item->getValue()/1.23)
                        ->setType($item->getType())
                        ->setName($itemName)
                    ;
            $invoice->addItem($invoiceItem);
        }
        $em->persist($invoice);
        $em->flush();

    }
    /**
     * Get item name
     *
     * @param string $type type
     * @return string
     */
    public function getItemName($type){
        switch ($type) {
            case PaymentItem::TYPE_PUBLICATION:
              return $this->services
                          ->getParameter('invoice_item_type_publication');

            case PaymentItem::TYPE_PROMO:
                return $this->services
                            ->getParameter('invoice_item_type_promo');

        }
    }
    /**
     * Get highest number invoice in this month
     *
     * @param string $type
     * @return null|int
     */
    public function getHighestInThisMonth($type) {
        $repo = $this->services->get('doctrine')
                     ->getRepository('AppPaymentBundle:Invoice');

        $prevInvoice = $repo->findOneBy(array('numberType'=>$type),
                                        array('saleDate'=>'DESC'));

        if(!is_object($prevInvoice)){
            return null;
        }

        $saleDate = $prevInvoice->getSaleDate();
        $now = new \DateTime();

        if($saleDate->format('m')==$now->format('m')){
            return $prevInvoice;
        }

        return null;

    }
    /**
     * Get invoices with pagination
     *
     * @param SearchInvoices $searchInvoices query params
     * @param bool $all get all
     * @return Invoice[]
     */
    public function getWidthPagination(SearchInvoices $searchInvoices,$all=false){
        $request = $this->services->get('request');
        $repo = $this->services->get('doctrine')->getRepository('AppPaymentBundle:Invoice');
        $qb = $repo->createQueryBuilder('i')
                   ->leftJoin('i.user', 'u')
                   ->addOrderBy('i.saleDate','DESC')
                ;

        if($searchInvoices->getNip()){
                $qb->andWhere('i.clientNip LIKE :nip')
                   ->setParameter('nip', '%'.$searchInvoices->getNip().'%');
        }
        if($searchInvoices->getType()){
                $qb->andWhere('i.numberType = :type')
                   ->setParameter('type', $searchInvoices->getType());
        }
        if($searchInvoices->getDateFrom()){
            $date = \DateTime::createFromFormat('Y-m-d', $searchInvoices->getDateFrom());
            $date->setTime(00,00,00);
            $qb->andWhere('i.saleDate >= :dateFrom')
               ->setParameter('dateFrom', $date);
        }
        if($searchInvoices->getDateTo()){
            $date = \DateTime::createFromFormat('Y-m-d', $searchInvoices->getDateTo());
            $date->setTime(23,59,59);
            $qb->andWhere('i.saleDate <= :dateTo')
               ->setParameter('dateTo', $date);
        }

        if($all){
            return $qb->getQuery()->getResult();
        }
        $paginator  = $this->services->get('knp_paginator');

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->get('page', 1)/*page number*/,
            $request->query->get('pp', 25)/*limit per page*/
        );
    }
    /**
     * Get invoice by id
     * 
     * @param int $id
     * @return Invoice
     */
    public function findById($id) {
        $repo = $this->services->get('doctrine')->getRepository('AppPaymentBundle:Invoice');

        return $repo->findOneById($id);
    }
}