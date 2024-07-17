<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Services;

use Symfony\Component\Templating\EngineInterface;
use App\OfferBundle\Model\BackOfficeSearch;
use App\OfferBundle\Services\OfferManager;
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
    private $offerManager;
    /**
     *
     * @var MpdfService mpdf
     */
    private $mpdf;
    /**
     * Constructor
     *
     * @param EngineInterface $templating templating
     * @param OfferManager $offerManager offer manager
     * @param type $mpdf mpdf
     */
    public function __construct(EngineInterface $templating, OfferManager $offerManager, $mpdf) {
      $this->templating = $templating;
      $this->offerManager = $offerManager;
      $this->mpdf = $mpdf;
    }
    /**
     * Generate csv response
     *
     * @param BackOfficeSearch $backOfficeSearch search parameters
     * @return Response response
     */
    public function generateCsvResponse(BackOfficeSearch $backOfficeSearch) {
            $filename = "export_offers_".date("Y_m_d").".csv";

            $html = $this->renderCsvContent($backOfficeSearch);
            $response = new Response($html);
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/csv;charset=UTF-8');
            $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename.'"');

            return $response;
    }
    /**
     * Generate pdf response
     *
     * @param BackOfficeSearch $backOfficeSearch search parameters
     * @return Response response
     */
    public function generatePdfResponse(BackOfficeSearch $backOfficeSearch) {
            $html = $this->renderPdfContent($backOfficeSearch);
            return $this->mpdf->generatePdfResponse($html);
    }
    /**
     * Render pdf content
     *
     * @param BackOfficeSearch $backOfficeSearch search parameters
     * @return string
     */
    private function renderPdfContent(BackOfficeSearch $backOfficeSearch){

        return $this->templating->render('AppOfferBundle:PDF:list.html.twig',array(
            'offers' => $this->offerManager->getWidthPagination($backOfficeSearch,true)
            ));
    }
    /**
     * Render csv content
     *
     * @param BackOfficeSearch $backOfficeSearch search parameters
     * @return string
     */
    private function renderCsvContent(BackOfficeSearch $backOfficeSearch){

        return $this->templating->render('AppOfferBundle:CSV:list.html.twig',array(
            'offers' => $this->offerManager->getWidthPagination($backOfficeSearch,true)
            ));
    }
}
