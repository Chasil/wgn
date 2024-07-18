<?php
/**
 * This file is part of the AppFrontPageBundle package.
 *
 */
namespace App\FrontPageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\OfferBundle\Model\Search;
use App\OfferBundle\Form\SearchType;

/**
 * Class StaticPageController
 *
 * @author wojciech przygoda
 */
class StaticPageController extends Controller
{
    /**
     * Show additional services page
     *
    */
    public function additionalServicesAction()
    {

        return $this->render('AppFrontPageBundle:StaticPage:additionalServices.html.twig',array(
            'types'=>$this->get('office.manager')->getAdditionalServicesByType(),
        ));
    }
    /**
     * Show sitemap
     *
    */
    public function sitemapAction()
    {
        return $this->render('AppFrontPageBundle:StaticPage:sitemap.html.twig');
    }
    /**
     * Show embed search page
     *
    */
    public function embedSearchAction() {
        return $this->render('AppFrontPageBundle:StaticPage:embedSearch.html.twig');
    }
    /**
     * Show subscription offers page
     *
    */
    public function subscriptionOffersAction() {
        $request = $this->getRequest();
        $search = $request->get('search');
        $searchManager = $this->get('search.manager');


        return $this->render('AppFrontPageBundle:StaticPage:subscriptionOffers.html.twig',array(
            'form'=>$searchManager->getFormView(),
        ));
    }
}
