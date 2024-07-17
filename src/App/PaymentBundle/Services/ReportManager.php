<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Services;

use Symfony\Component\Templating\EngineInterface;
use App\PaymentBundle\Model\SearchPayments;
use App\UserBundle\Entity\User;
use App\PaymentBundle\Services\PaymentManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ReportManager
 *
 * @author wojciech przygoda
 */
class ReportManager {
    /**
     *
     * @var EngineInterface templating templating
     */
    private $templating;
    /**
     *
     * @var OfferManager offer manager
     */
    private $paymentManager;
    /**
     *
     * @var MpdfService mpdf
     */
    private $mpdf;
    /**
     * Constructor
     *
     * @param EngineInterface $templating templating
     * @param PaymentManager $paymentManager payment manager
     * @param type $mpdf mpdf
     */
    public function __construct(EngineInterface $templating, PaymentManager $paymentManager, $mpdf) {
      $this->templating = $templating;
      $this->paymentManager = $paymentManager;
      $this->mpdf = $mpdf;
    }
    /**
     * Generate csv response
     *
     * @param SearchPayments $searchPayments search parameters
     * @return Response response
     */
    public function generateCsvResponse(SearchPayments $searchPayments) {
            $filename = "export_payments_".date("Y_m_d").".csv";

            $html = $this->renderCsvContent($searchPayments);
            $response = new Response($html);
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/csv;charset=UTF-8');
            $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename.'"');

            return $response;
    }
    /**
     * Generate pdf response
     *
     * @param SearchPayments $searchUsers search parameters
     * @return Response response
     */
    public function generatePdfResponse(SearchPayments $searchUsers) {
            $html = $this->renderPdfContent($searchUsers);
            return $this->mpdf->generatePdfResponse($html);
    }
    /**
     * Render pdf content
     *
     * @param SearchPayments $searchPayments search parameters
     * @return string
     */
    private function renderPdfContent(SearchPayments $searchPayments){


        return $this->templating->render('AppPaymentBundle:PDF:list.html.twig',
                array(
                    'payments' => $this->paymentManager->getWidthPagination($searchPayments,true)
                ));
    }
    /**
     * Render csv content
     *
     * @param SearchPayments $searchUsers search parameters
     * @return string
     */
    private function renderCsvContent(SearchPayments $searchUsers){

        return $this->templating->render('AppPaymentBundle:CSV:list.html.twig',array(
            'payments' => $this->paymentManager->getWidthPagination($searchUsers,true)
            ));
    }
}
