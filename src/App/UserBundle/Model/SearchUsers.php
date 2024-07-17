<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Model;

use App\UserBundle\Entity\User;

/**
 * Class  SearchUsers
 *
 * @author wojciech przygoda
 */
class SearchUsers {

    /**
     *
     * @var string role
     */
    protected $role;

    /**
     *
     * @var string email
     */
    protected $email;

    /**
     *
     * @var string username
     */
    protected $username;

    /**
     *
     * @var string name
     */
    protected $name;

    /**
     *
     * @var string type
     */
    protected $type;

    /**
     *
     * @var array permitted roles
     */
    protected $permittedRoles;

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
     * Constructor
     */
    public function __construct() {
        $this->type = User::TYPE_USERS;
        $this->setPermittedRoles();
    }

    /**
     *
     * Get role
     *
     * @return string
     */
    public function getRole() {
        return $this->role;
    }

    /**
     *
     * Set role
     *
     * @param string $role role
     * @return SearchUsers
     */
    public function setRole($role) {
        $this->role = $role;
        return $this;
    }

    /**
     *
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     *
     * Set email
     *
     * @param string $email email
     * @return SearchUsers
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    /**
     *
     * Get role
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     *
     * Set username
     *
     * @param string $username username
     * @return SearchUsers
     */
    public function setUsername($username) {
        $this->username = $username;
        return $this;
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
     * Set name
     *
     * @param string $name name
     * @return SearchUsers
     */
    public function setName($name) {
        $this->name = $name;
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
     * @return SearchUsers
     */
    public function setType($type) {
        $this->type = $type;
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
     * Get dateTo
     *
     * @return \DateTime
     */
    public function getDateTo() {
        return $this->dateTo;
    }

    /**
     *
     * Set dateFrom
     *
     * @param \DateTime $dateFrom date from
     * @return SearchUsers
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
     * @return SearchUsers
     */
    public function setDateTo($dateTo) {
        $this->dateTo = $dateTo;
        return $this;
    }

    /**
     *
     * Set permittedRoles
     */
    public function setPermittedRoles(){
        switch($this->type){
            case User::TYPE_CLIENTS:
                $this->permittedRoles = User::$clientRoles;
            break;
            case User::TYPE_AGENTS:
                $this->permittedRoles = User::$agentRoles;
            break;
            case User::TYPE_OFFICE_MANAGER:
                $this->permittedRoles = User::$officeManagerRoles;
            break;
            case User::TYPE_USERS:
            default:
                $this->permittedRoles = User::$userRoles;
            break;

        }
    }


    /**
     *
     * Get permittedRoles
     *
     * @return array
     */
    public function getPermittedRoles() {
        return $this->permittedRoles;
    }
    /**
     *
     * Check if role is in permitted roles
     *
     * @return bool
     */
    public function isRolePermitted() {
        return in_array($this->role,$this->permittedRoles);
    }
}