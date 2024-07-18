<?php
/**
 * This file is part of the AppBackOfficeBundle package.
 *
 */
namespace App\BackOfficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/**
 * Class DashboardController
 *
 * @author wojciech przygoda
 */
class DashboardController extends Controller
{
    /**
     * Show dashboard
     *
    */
    public function showAction()
    {
        return $this->render('AppBackOfficeBundle:Dashboard:show.html.twig');
    }
}
