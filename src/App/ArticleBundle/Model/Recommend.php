<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Model;

/**
 * Class Recommend
 *
 * @author wojciech przygoda
 */
class Recommend {

    /**
     *
     * @var string massage
     */
    protected $message;

    /**
     *
     * @var string email
     */
    protected $email;

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
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
    /**
     *
     * Set message
     *
     * @param type $message message
     * @return Recommend
     */
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    /**
     *
     * Set email
     *
     * @param string $email email
     * @return Recommend
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }
}
