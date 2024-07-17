<?php

namespace App\FrontPageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppFrontPageBundle:Default:index.html.twig');
    }
}
