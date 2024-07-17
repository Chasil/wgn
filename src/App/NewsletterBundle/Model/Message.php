<?php
/**
 * This file is part of the AppNewsletterBundle package.
 *
 */
namespace App\NewsletterBundle\Model;

/**
 * Class Message
 *
 * @author wojciech przygoda
 */
class Message {

    /**
     *
     * @var string subject
     */
    protected $subject;
    /**
     *
     * @var string message
     */
    protected $message;

    /**
     * Constructor
     */
    public function __construct() {

    }

    /**
     *
     * Get company
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
     * @return Message
     */
    public function setSubject($subject) {
        $this->subject = $subject;
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
     * @param string message 
     * @return Message
     */
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

}