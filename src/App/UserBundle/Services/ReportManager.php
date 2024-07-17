<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Services;

use Symfony\Component\Templating\EngineInterface;
use App\UserBundle\Model\SearchUsers;
use App\UserBundle\Entity\User;
use App\UserBundle\Services\UserManager;
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
     * @var UserManager user manager
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
     * @param UserManager $userManager payment manager
     * @param type $mpdf mpdf
     */
    public function __construct(EngineInterface $templating, UserManager $userManager, $mpdf) {
      $this->templating = $templating;
      $this->userManager = $userManager;
      $this->mpdf = $mpdf;
    }
    /**
     * Generate csv response
     *
     * @param SearchUsers $searchUsers search parameters
     * @return Response response
     */
    public function generateCsvResponse(SearchUsers $searchUsers) {
            $filename = "export_".$searchUsers->getType()."_".date("Y_m_d").".csv";

            $html = $this->renderCsvContent($searchUsers);
            $response = new Response($html);
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/csv;charset=UTF-8');
            $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename.'"');

            return $response;
    }
    /**
     * Generate pdf response
     *
     * @param SearchUsers $searchUsers search parameters
     * @return Response response
     */
    public function generatePdfResponse(SearchUsers $searchUsers) {
            $html = $this->renderPdfContent($searchUsers);
            return $this->mpdf->generatePdfResponse($html);
    }
    /**
     * Render pdf content
     *
     * @param SearchUsers $searchUsers search parameters
     * @return string
     */
    private function renderPdfContent(SearchUsers $searchUsers){
        switch ($searchUsers->getType()){
            case User::TYPE_AGENTS:
                $template = 'AppUserBundle:PDF:listAgents.html.twig';
            break;
            case User::TYPE_CLIENTS:
                $template = 'AppUserBundle:PDF:listClients.html.twig';
            break;
            case User::TYPE_USERS:
                $template = 'AppUserBundle:PDF:listUsers.html.twig';
            break;
        }
        return $this->templating->render($template,array(
            'users' => $this->userManager->getWidthPagination($searchUsers,true)
            ));
    }
    /**
     * Render csv content
     *
     * @param SearchUsers $searchUsers search parameters
     * @return string
     */
    private function renderCsvContent(SearchUsers $searchUsers){
        switch ($searchUsers->getType()){
            case User::TYPE_AGENTS:
                $template = 'AppUserBundle:CSV:listAgents.html.twig';
            break;
            case User::TYPE_CLIENTS:
                $template = 'AppUserBundle:CSV:listClients.html.twig';
            break;
            case User::TYPE_USERS:
                $template = 'AppUserBundle:CSV:listUsers.html.twig';
            break;
        }
        return $this->templating->render($template,array(
            'users' => $this->userManager->getWidthPagination($searchUsers,true)
            ));
    }
}
