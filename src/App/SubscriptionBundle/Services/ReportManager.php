<?php
/**
 * This file is part of the AppSubscriptionBundle package.
 *
 */
namespace App\SubscriptionBundle\Services;

use Symfony\Component\Templating\EngineInterface;
use App\SubscriptionBundle\Model\SearchSubscriptions;
use App\SubscriptionBundle\Services\SubscriptionManager;
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
    private $userManager;
    /**
     *
     * @var MpdfService mpdf
     */
    private $mpdf;
    /**
     * Constructor
     *
     * @param EngineInterface $templating templating
     * @param SubscriptionManager $subscriptionManager payment manager
     * @param type $mpdf mpdf
     */
    public function __construct(EngineInterface $templating, SubscriptionManager $subscriptionManager, $mpdf) {
      $this->templating = $templating;
      $this->userManager = $subscriptionManager;
      $this->mpdf = $mpdf;
    }
    /**
     * Generate csv response
     *
     * @param SearchSubscriptions $searchSubscriptions search parameters
     * @return Response response
     */
    public function generateCsvResponse(SearchSubscriptions $searchSubscriptions) {
            $filename = "export_abonamentow_".date("Y_m_d").".csv";

            $html = $this->renderCsvContent($searchSubscriptions);
            $response = new Response($html);
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/csv;charset=UTF-8');
            $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename.'"');

            return $response;
    }
    /**
     * Generate pdf response
     *
     * @param SearchSubscriptions $searchSubscriptions search parameters
     * @return Response response
     */
    public function generatePdfResponse(SearchSubscriptions $searchSubscriptions) {
            $html = $this->renderPdfContent($searchSubscriptions);
            return $this->mpdf->generatePdfResponse($html);
    }
    /**
     * Render pdf content
     *
     * @param SearchSubscriptions $searchSubscriptions search parameters
     * @return string
     */
    private function renderPdfContent(SearchSubscriptions $searchSubscriptions){

        return $this->templating->render('AppSubscriptionBundle:PDF:list.html.twig',array(
            'pagination' => $this->userManager->getWidthPagination($searchSubscriptions,true)
            ));
    }
    /**
     * Render csv content
     *
     * @param SearchSubscriptions $searchSubscriptions search parameters
     * @return string
     */
    private function renderCsvContent(SearchSubscriptions $searchSubscriptions){
        return $this->templating->render('AppSubscriptionBundle:CSV:list.html.twig',array(
            'pagination' => $this->userManager->getWidthPagination($searchSubscriptions,true)
            ));
    }
}
