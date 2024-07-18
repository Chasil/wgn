<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Model;

/**
 * Class SearchOffices
 *
 * @author wojciech przygoda
 */
class SearchOffices {

    /**
     *
     * @var string email
     */
    protected $email;

    /**
     *
     * @var string name
     */
    protected $name;

    /**
     *
     * @var string city
     */
    protected $city;

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
     * Set email
     * 
     * @param string $email email
     * @return earchOffices
     */
    public function setEmail($email) {
        $this->email = $email;
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
     * Set name
     *
     * @param string $name name
     * @return earchOffices
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     *
     * Get city
     *
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * Set city
     *
     * @param string $city city
     * @return earchOffices
     */
    public function setCity($city) {
        $this->city = $city;
        return $this;
    }
}