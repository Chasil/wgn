<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Model;

use App\UserBundle\Entity\User;
/**
 * Class Contact
 *
 * @author wojciech przygoda
 */
class Contact {

    /**
     *
     * @var string subject
     */
    protected $subject;

    /**
     *
     * @var string name
     */
    protected $name;

    /**
     *
     * @var string message
     */
    protected $message;

    /**
     *
     * @var User user
     */
    protected $user;

    /**
     *
     * Get subject
     *
     * @return string
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     *
     * Set subject
     *
     * @param string $subject subject
     * @return Contact
     */
    public function setSubject($subject) {
        $this->subject = $subject;
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
     * @return Contact
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     *
     * Get message
     *
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     *
     * Set message
     *
     * @param string $message message
     * @return Contact
     */
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    /**
     *
     * Get user
     *
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     *
     * Set user
     *
     * @param User $user user
     * @return Contact
     */
    public function setUser($user) {
        $this->user = $user;
        return $this;
    }
}
