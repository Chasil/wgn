<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Services;

use App\PaymentBundle\Entity\Payment;
/**
 * Interface PaymentMethodInterface
 *
 * @author wojciech przygoda
 */
interface PaymentMethodInterface {
    /**
     * Process payment
     * 
     * @param Payment $payment payment
     * @param string $type payment type
     */
    public function process(Payment $payment, $type);
}
