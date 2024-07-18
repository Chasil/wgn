<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Model;

use App\OfferBundle\Entity\Offer;

/**
 * Class Contact
 *
 * @author wojciech przygoda
 */
class Contact {

    /**
     *
     * @var Offer offer
     */
    protected $offer;

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
     * @var bool agree
     */
    protected $agree;


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
     * @return Contact
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }
    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     *
     * Set phone
     *
     * @param string $phone phone
     * @return Contact
     */
    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }

    /**
     *
     * Get agree
     *
     * @return bool
     */
    public function getAgree() {
        return $this->agree;
    }
    /**
     *
     * Set Agree
     *
     * @param bool $agree agree
     * @return Contact
     */
    public function setAgree($agree) {
        $this->agree = $agree;
        return $this;
    }

    /**
     *
     * Get offer
     *
     * @return Offer
     */
    function getOffer() {
        return $this->offer;
    }

    /**
     *
     * Set offer
     *
     * @param Offer $offer offer
     * @return Contact
     */
    function setOffer(Offer $offer) {
        $this->offer = $offer;
        $this->message = $this->generateDefaultMessage();
        return $this;
    }

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
     * Generate default message
     * 
     * @return string
     * @throws Exception
     */
    private function generateDefaultMessage(){
        if(!is_object($this->offer)){
            throw new Exception('Offer is not set');
        }
        $message = "";
        $category = $this->offer->getCategory();

        if(!is_object($category)){
            return;
        }
        $type = (string)$this->offer->getType();
        $rooms = $this->offer->getRooms() . ' ';
        $last = substr((string)$this->offer->getRooms(), -1);

        if((int)$last == 1 && $this->offer->getRooms()< 10){
            $rooms .= 'pokój';
        }
        else if((int)$last >1 && (int)$last < 5){
            $rooms .= 'pokoje';
        }else if((int)$last > 4 || (int)$last == 0){
            $rooms .= 'pokoi';
        }

        switch($this->offer->getCategory()->getUniqueKey()){

            case 'flat':
                $message .= ($type !="") ? $type .", " : "Mieszkanie, " ;
                $message .= $this->offer->getCity() . ", ";
                $message .= $rooms;
                $message .= ", " . $this->offer->getSquere() . " m²";
                $message .= ($this->offer->getFloor()) ? ", " . $this->offer->getFloor() . " piętro" : "";
            break;
            case 'garage':
                $message .= ($type !="") ? $type .", " : "Garaż, " ;
                $message .= $this->offer->getCity();
                $message .= ", " . $this->offer->getSquere() . " m²";
            break;
            case 'house':
                $message .= ($type !="") ? $type .", " : "Dom, " ;
                $message .= $this->offer->getCity() . ", ";
                $message .= $rooms;
                $message .= ", " . $this->offer->getSquere() . " m²";
            break;
            case 'local':
                $message .= ($type !="") ? $type .", " : "Lokal, " ;
                $message .= $this->offer->getCity();
                $message .= ", " . $this->offer->getSquere() . " m²";
            break;
            case 'plot':
                $message .= ($type !="") ? $type .", " : "Działka, " ;
                $message .= $this->offer->getCity();
                $message .= ", " . $this->offer->getSquere() . " m²";
            break;
            case 'commercial':
                $message .= ($type !="") ? $type .", " : "Obiekt komercyjny, " ;
                $message .= $this->offer->getCity();
                $message .= ", " . $this->offer->getSquere() . " m²";
            break;
        }
        $message .= ". Chętnie poznam więcej szczegółów";
        $message .= ".\nProszę o kontakt.";

        return $message;
    }

}
