<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Model;

/**
 * Class Order
 *
 * @author wojciech przygoda
 */
class Order {

    /**
     *
     * @var string login
     */
    protected $login;

    /**
     *
     * @var string contact person
     */
    protected $contactPerson;

    /**
     *
     * @var string nip
     */
    protected $nip;

    /**
     *
     * @var string package
     */
    protected $package;

    /**
     *
     * @var bool agree regulations
     */
    protected $agreeRegulations;

    /**
     *
     * @var bool agree processing data
     */
    protected $agreeProcessingData;

    /**
     *
     * Get login
     *
     * @return string
     */
    public function getLogin() {
        return $this->login;
    }

    /**
     *
     * Get contactPerson
     *
     * @return string
     */
    public function getContactPerson() {
        return $this->contactPerson;
    }

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
     * Set login
     *
     * @param string $login login
     * @return Order
     */
    public function setLogin($login) {
        $this->login = $login;
        return $this;
    }

    /**
     *
     * Set contactPerson
     *
     * @param string $contactPerson contact person
     * @return Order
     */
    public function setContactPerson($contactPerson) {
        $this->contactPerson = $contactPerson;
        return $this;
    }

    /**
     *
     * Set nip
     *
     * @param string $nip nip
     * @return Order
     */
    public function setNip($nip) {
        $this->nip = $nip;
        return $this;
    }

    /**
     *
     * Get package
     *
     * @return string
     */
    public function getPackage() {
        return $this->package;
    }

    /**
     *
     * Set package
     *
     * @param string $package package
     * @return Order
     */
    public function setPackage($package) {
        $this->package = $package;
        return $this;
    }

    /**
     *
     * Get agreeRegulations
     *
     * @return bool
     */
    public function getAgreeRegulations() {
        return $this->agreeRegulations;
    }

    /**
     *
     * Set agreeRegulations
     *
     * @param bool $agreeRegulations agree regulations
     * @return Order
     */
    public function setAgreeRegulations($agreeRegulations) {
        $this->agreeRegulations = $agreeRegulations;
        return $this;
    }

    /**
     *
     * Get agreeProcessingData
     *
     * @return bool
     */
    public function getAgreeProcessingData() {
        return $this->agreeProcessingData;
    }

    /**
     *
     * Set agreeProcessingData
     *
     * @param bool $agreeProcessingData agree processing data
     * @return Order
     */
    public function setAgreeProcessingData($agreeProcessingData) {
        $this->agreeProcessingData = $agreeProcessingData;
        return $this;
    }





}
