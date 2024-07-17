<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Model;

use App\UserBundle\Entity\User;
use App\OfficeBundle\Entity\Office;

/**
 * Class  SearchOfficeManagers
 *
 * @author wojciech przygoda
 */
class SearchOfficeManagers extends SearchUsers {

    /**
     *
     * @var Office office
     */
    protected $office;

    /**
     * Constructor
     */
    public function __construct() {
        $this->type = User::TYPE_OFFICE_MANAGER;
        $this->setPermittedRoles();
    }

    /**
     *
     * Get office
     *
     * @return Office
     */
    public function getOffice() {
        return $this->office;
    }

    /**
     *
     * Set office
     *
     * @param Office $office office
     * @return SearchOfficeManagers
     */
    public function setOffice($office) {
        $this->office = $office;
        return $this;
    }


}