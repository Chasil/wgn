<?php
/**
 * This file is part of the AppNewsletterBundle package.
 *
 */
namespace App\NewsletterBundle\Model;

/**
 * Class SearchEmails
 *
 * @author wojciech przygoda
 */
class SearchEmails {

    /**
     *
     * @var string email
     */
    protected $email;

    /**
     * Constructor
     */
    public function __construct() {

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
     * @return SearchEmails
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

}