<?php

namespace App\ArchiveBundle\Controller;

use App\OfficeBundle\Entity\Office;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontPageController extends Controller
{
    public function showAction()
    {
        $settings = $this->get('settings.manager')->get(1);

        return $this->render('AppArchiveBundle:FrontPage:show.html.twig', [
            'mainImage'=> $this->get('myimage.manager')->getRandom(),
            'offices'=>$offices = $this->get('office.manager')->getAll(Office::TYPE_PROPERTIES),
            'h1'=>$settings->getH1(),
            'h1TextColor'=>$settings->getH1TextColor(),
            'h1Color'=>$settings->getH1Color(),
        ]);
    }
}
