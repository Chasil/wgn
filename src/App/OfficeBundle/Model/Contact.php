<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Model;

use App\OfficeBundle\Entity\Office;

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
     * @var Office office
     */
    protected $office;

    /**
     *
     * @var string email
     */
    protected $email;

    /**
     *
     * @var string phone
     */
    protected $phone;
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
     * Get office
     *
     * @return string
     */
    public function getOffice() {
        return $this->office;
    }

    /**
     *
     * Set office
     *
     * @param string $office office
     * @return Contact
     */
    public function setOffice($office) {
        $this->office = $office;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Contact
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return Contact
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

}
