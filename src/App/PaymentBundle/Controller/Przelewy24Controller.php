<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Class Przelewy24Controller
 *
 * @author wojciech przygoda
 */
class Przelewy24Controller extends Controller
{
    /**
     * Check payment status
     *
    */
    public function statusAction()
    {
        $przelewy24Manager = $this->get('card.manager');
        $przelewy24Manager->confirm();
        return new JsonResponse(array('success'=>true));
    }
    /**
     * Check payment status form promo offers
     *
    */
    public function promoStatusAction()
    {
        $przelewy24Manager = $this->get('card.manager');
        $przelewy24Manager->confirmPromo();
        return new JsonResponse(array('success'=>true));
    }
}
