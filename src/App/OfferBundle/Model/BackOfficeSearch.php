<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Model;

use App\OfferBundle\Entity\Category;
use App\OfferBundle\Entity\TransactionType;

/**
 * Class BackOfficeSearch
 *
 * @author wojciech przygoda
 */
class BackOfficeSearch {

    /**
     *
     * @var string signature
     */
    protected $signature;

    /**
     *
     * @var bool publication state
     */
    protected $isPublish;

    /**
     *
     * @var Category category
     */
    protected $category;

    /**
     *
     * @var string name
     */
    protected $name;

    /**
     *
     * @var TransactionType transaction type
     */
    protected $transactionType;

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
     * @return BackOfficeSearch
     */
    public function setSignature($signature) {
        $this->signature = $signature;
        return $this;
    }

    /**
     * Get isPublish
     * 
     * @return bool
     */
    public function getIsPublish() {
        return $this->isPublish;
    }

    /**
     *
     * Set isPublish
     *
     * @param type $isPublish publication state
     * @return BackOfficeSearch
     */
    public function setIsPublish($isPublish) {
        $this->isPublish = $isPublish;
        return $this;
    }

    /**
     *
     * Get category
     *
     * @return Category
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     *
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     *
     * Set category
     *
     * @param Category $category category
     * @return BackOfficeSearch
     */
    public function setCategory($category) {
        $this->category = $category;
        return $this;
    }

    /**
     *
     * Set name
     *
     * @param string $name name
     * @return BackOfficeSearch
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     *
     * Get transaction type
     *
     * @return TransactionType
     */
    public function getTransactionType() {
        return $this->transactionType;
    }
    /**
     *
     * Set transactionType
     *
     * @param TransactionType $transactionType transaction type
     * @return BackOfficeSearch
     */
    public function setTransactionType($transactionType) {
        $this->transactionType = $transactionType;
        return $this;
    }

    /**
     * Get date form
     *
     * @return \DateTime
     */
    public function getDateFrom() {
        return $this->dateFrom;
    }

    /**
     * Get date to
     *
     * @return \DateTime
     */
    public function getDateTo() {
        return $this->dateTo;
    }

    /**
     * Set dateForm
     *
     * @param \DateTime $dateFrom date form
     * @return BackOfficeSearch
     */
    public function setDateFrom($dateFrom) {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    /**
     *
     * Set dateTo
     *
     * @param \DateTime $dateTo date to
     * @return BackOfficeSearch
     */
    public function setDateTo($dateTo) {
        $this->dateTo = $dateTo;
        return $this;
    }


}
