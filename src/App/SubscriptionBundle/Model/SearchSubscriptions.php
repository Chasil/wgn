<?php
/**
 * This file is part of the AppSubscriptionBundle package.
 *
 */
namespace App\SubscriptionBundle\Model;

/**
 * Class SearchSubscriptions
 *
 * @author wojciech przygoda
 */
class SearchSubscriptions {


    /**
     *
     * @var string username
     */
    protected $username;

    /**
     *
     * @var int state
     */
    protected $state;

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
     * Get username
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
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
     * Set username
     *
     * @param string $username username
     * @return SearchSubscriptions
     */
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    /**
     *
     * Set state
     *
     * @param string $state state
     * @return SearchSubscriptions
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
     * @param \DateTime $dateFrom date form
     * @return SearchSubscriptions
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
     * @return SearchSubscriptions
     */
    public function setDateTo($dateTo) {
        $this->dateTo = $dateTo;
        return $this;
    }
}