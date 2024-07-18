<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Model;

use App\UserBundle\Entity\User;
use App\UserBundle\Model\SearchUsers;
use App\OfficeBundle\Entity\Office;

/**
 * Class SearchAgents
 *
 * @author wojciech przygoda
 */
class SearchAgents extends SearchUsers {

    /**
     *
     * @var Office office
     */
    protected $office;

    /**
     * Constructor
     */
    public function __construct() {
        $this->type = User::TYPE_AGENTS;
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
     * Set agents
     * 
     * @param Office $office office
     * @return SearchAgents
     */
    public function setOffice($office) {
        $this->office = $office;
        return $this;
    }


}