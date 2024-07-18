<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Services;

use Symfony\Component\Templating\EngineInterface;
use App\OfficeBundle\Model\SearchOffices;
use App\OfficeBundle\Services\OfficeManager;
use Symfony\Component\HttpFoundation\Response;
/**
 * Class ReportManager
 *
 * @author wojciech przygoda
 */
class ReportManager {
    /**
     *
     * @var EngineInterface templating
     */
    private $templating;
    /**
     *
     * @var OfficeManager office manager
     */
    private $officeManager;
    /**
     *
     * @var MpdfService mpdf
     */
    private $mpdf;
    /**
     * Constructor
     *
     * @param EngineInterface $templating templating
     * @param OfficeManager $officeManager office manager
     * @param type $mpdf mpdf
     */
    public function __construct(EngineInterface $templating, OfficeManager $officeManager, $mpdf) {
      $this->templating = $templating;
      $this->officeManager = $officeManager;
      $this->mpdf = $mpdf;
    }
    /**
     * Generate csv response
     *
     * @param BackOfficeSearch $searchOffices search parameters
     * @return Response response
     */
    public function generateCsvResponse(SearchOffices $searchOffices) {
            $filename = "export_offices".date("Y_m_d").".csv";

            $html = $this->renderCsvContent($searchOffices);
            $response = new Response($html);
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/csv;charset=UTF-8');
            $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename.'"');

            return $response;
    }
    /**
     * Generate pdf response
     *
     * @param BackOfficeSearch $searchOffices search parameters
     * @return Response response
     */
    public function generatePdfResponse(SearchOffices $searchOffices) {
            $html = $this->renderPdfContent($searchOffices);
            return $this->mpdf->generatePdfResponse($html);
    }
    /**
     * Render pdf content
     *
     * @param BackOfficeSearch $searchOffices search parameters
     * @return string
     */
    private function renderPdfContent(SearchOffices $searchOffices){

        return $this->templating->render('AppOfficeBundle:PDF:list.html.twig',array(
            'offices' => $this->officeManager->getWidthPagination($searchOffices,true)
            ));
    }
    /**
     * Render csv content
     *
     * @param BackOfficeSearch $searchOffices search parameters
     * @return string
     */
    private function renderCsvContent(SearchOffices $searchOffices){

        return $this->templating->render('AppOfficeBundle:CSV:list.html.twig',array(
            'offices' => $this->officeManager->getWidthPagination($searchOffices,true)
            ));
    }
}
