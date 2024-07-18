<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Model;

use App\UserBundle\Entity\User;

/**
 * Class  SearchClients
 *
 * @author wojciech przygoda
 */
class SearchClients extends SearchUsers {

    /**
     *
     * @var string company
     */
    protected $company;

    /**
     *
     * @var string nip
     */
    protected $nip;

    /**
     *
     * @var bool isCompany
     */
    protected $isCompany;

    /**
     * Constructor
     */
    public function __construct() {
        $this->type = User::TYPE_CLIENTS;
        $this->setPermittedRoles();
    }

    /**
     *
     * Get company
     *
     * @return string
     */
    public function getCompany() {
        return $this->company;
    }

    /**
     *
     * Set company
     *
     * @param string $company company
     * @return SearchClients
     */
    public function setCompany($company) {
        $this->company = $company;
        return $this;
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
     * Set nip
     *
     * @param string $nip nip
     * @return SearchClients
     */
    public function setNip($nip) {
        $this->nip = $nip;
        return $this;
    }

    /**
     *
     * Get isCompany
     *
     * @return bool
     */
    public function getIsCompany() {
        return $this->isCompany;
    }

    /**
     *
     * Set isCompany
     *
     * @param bool $isCompany is company
     * @return SearchClients
     */
    public function setIsCompany($isCompany) {
        $this->isCompany = $isCompany;
        return $this;
    }
}