<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class RedirectController
 *
 * @author wojciech przygoda
 */
class RedirectController extends Controller
{
    /**
     * Redirect old offers
     *
     * @param string $signature
     * @return RedirectResponse
     */
    public function offerAction($signature){
        return $this->redirectToRoute('frontend_offer_list',
                array('search'=>array('signatureLike'=>$signature)), 301);
    }
}
