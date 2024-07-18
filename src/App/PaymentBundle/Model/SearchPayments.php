<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Model;
/**
 * Class SearchPayments
 *
 * @author wojciech przygoda
 */
class SearchPayments {

    /**
     *
     * @var string payment method
     */
    protected $paymentMethod;

    /**
     *
     * @var string signature
     */
    protected $signature;

    /**
     *
     * @var int state
     */
    protected $state;

    /**
     *
     * @var \DateTime date form
     */
    protected $dateFrom;

    /**
     *
     * @var \DateTime date to
     */
    protected $dateTo;

    /**
     *
     * Get paymentMethod
     *
     * @return string
     */
    public function getPaymentMethod() {
        return $this->paymentMethod;
    }
    /**
     *
     * Set paymentMethod
     *
     * @param string $paymentMethod payment method
     * @return SearchPayments
     */
    public function setPaymentMethod($paymentMethod) {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     *
     * Get signature
     *
     * @return string
     */
    public function getSignature() {
        return $this->signature;
    }

    /**
     *
     * Set signature
     *
     * @param string $signature signature
     * @return SearchPayments
     */
    public function setSignature($signature) {
        $this->signature = $signature;
        return $this;
    }

    /**
     *
     * Get state
     *
     * @return int
     */
    public function getState() {
        return $this->state;
    }

    /**
     *
     * Set state
     *
     * @param string $state state
     * @return SearchPayments
     */
    public function setState($state) {
        $this->state = $state;
        return $this;
    }

    /**
     *
     * Get dateFrom
     *
     * @return \DateTime
     */
    public function getDateFrom() {
        return $this->dateFrom;
    }

    /**
     *
     * Set dateFrom
     *
     * @param \DateTime $dateFrom date from
     * @return SearchPayments
     */
    public function setDateFrom($dateFrom) {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    /**
     *
     * Get dateTo
     *
     * @return \DateTime
     */
    public function getDateTo() {
        return $this->dateTo;
    }

    /**
     *
     * Set dateTo
     *
     * @param \DateTime $dateTo date to
     * @return SearchPayments
     */
    public function setDateTo($dateTo) {
        $this->dateTo = $dateTo;
        return $this;
    }
}
