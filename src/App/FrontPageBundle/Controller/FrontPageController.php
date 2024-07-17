<?php
/**
 * This file is part of the AppFrontPageBundle package.
 *
 */
namespace App\FrontPageBundle\Controller;

use App\OfficeBundle\Entity\Office;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\ArticleBundle\Entity\ArticleCategory;
use App\ArticleBundle\Entity\Article;
use App\ArticleBundle\Entity\Tag;

/**
 * Class FrontPageController
 *
 * @author wojciech przygoda
 */
class FrontPageController extends Controller
{
    /**
     * Show front page
     *
    */
    public function showAction()
    {
        $settings = $this->get('settings.manager')->get(1);

        return $this->render('AppFrontPageBundle:FrontPage:show.html.twig', [
            'mainImage'=> $this->get('myimage.manager')->getRandom(),
            'offices'=>$offices = $this->get('office.manager')->getAll(Office::TYPE_PROPERTIES),
            'officesInSpain'=>$offices = $this->get('office.manager')->getAll(Office::TYPE_PROPERTIES, 1, 'es'),
            'h1'=>$settings->getH1(),
            'h1TextColor'=>$settings->getH1TextColor(),
            'h1Color'=>$settings->getH1Color(),
        ]);
    }
}
