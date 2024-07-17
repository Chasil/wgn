<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Model;
/**
 * Class SearchInvoices
 *
 * @author wojciech przygoda
 */
class SearchInvoices {

    /**
     *
     * @var string nip
     */
    protected $nip;

    /**
     *
     * @var string type
     */
    protected $type;

    /**
     *
     * @var \DateTime date from
     */
    protected $dateFrom;

    /**
     *
     * @var \DateTime date to
     */
    protected $dateTo;

    /**
     *
     * Get nip
     *
     * @return string
     */
    public function getNip() {
        return $this->nip;
    }

    /**
     *
     * Set nip
     *
     * @param string $nip nip
     * @return SearchInvoices
     */
    public function setNip($nip) {
        $this->nip = $nip;
        return $this;
    }

    /**
     *
     * Get type
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     *
     * Set type
     *
     * @param string $type type
     * @return SearchInvoices
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     *
     * Get dateForm
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
     * @param \DateTime $dateFrom date form
     * @return SearchInvoices
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
     * @return SearchInvoices
     */
    public function setDateTo($dateTo) {
        $this->dateTo = $dateTo;
        return $this;
    }
}
