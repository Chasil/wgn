<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Model;

use App\OfferBundle\Entity\Category;
use App\OfferBundle\Entity\Country;
use App\OfferBundle\Entity\Type;

/**
 * Class LookingFor
 *
 * @author wojciech przygoda
 */
class LookingFor {
    /**
     *
     * @var string transaction
     */
    protected $transaction;

    /**
     *
     * @var Category category
     */
    protected $category;

    /**
     *
     * @var Type type
     */
    protected $type;

    /**
     *
     * @var Country country
     */
    protected $country;

    /**
     *
     * @var string province
     */
    protected $province;

    /**
     *
     * @var string city
     */
    protected $city;

    /**
     *
     * @var string squere from
     */
    protected $squereFrom;

    /**
     *
     * @var string squere to
     */
    protected $squereTo;

    /**
     *
     * @var string rooms from
     */
    protected $roomsFrom;

    /**
     *
     * @var string rooms to
     */
    protected $roomsTo;

    /**
     *
     * @var string floor from
     */
    protected $floorFrom;

    /**
     *
     * @var string floor to
     */
    protected $floorTo;

    /**
     *
     * @var string price from
     */
    protected $priceFrom;

    /**
     *
     * @var string price to
     */
    protected $priceTo;

    /**
     *
     * @var string currency
     */
    protected $currency;

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
     * @var string phone
     */
    protected $phone;

    /**
     *
     * @var string person country
     */
    protected $personCountry;

    /**
     *
     * @var string person city
     */
    protected $personCity;

    /**
     *
     * @var string person street
     */
    protected $personStreet;

    /**
     *
     * @var string message
     */
    protected $message;

    /**
     *
     * Get transaction
     *
     * @return string
     */
    public function getTransaction() {
        return $this->transaction;
    }

    /**
     *
     * Set transaction
     *
     * @param string $transaction transaction
     * @return LookingFor
     */
    public function setTransaction($transaction) {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     *
     * Get category
     *
     * @return Category
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     *
     * Set category
     *
     * @param Category $category category
     * @return LookingFor
     */
    public function setCategory($category) {
        $this->category = $category;
        return $this;
    }

    /**
     *
     * Get type
     *
     * @return Type
     */
    public function getType() {
        return $this->type;
    }

    /**
     *
     * Set type
     *
     * @param Type $type type
     * @return LookingFor
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }
    /**
     *
     * Get country
     *
     * @return Country
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     *
     * Set country
     *
     * @param Country $country country
     * @return LookingFor
     */
    public function setCountry($country) {
        $this->country = $country;
        return $this;
    }

    /**
     *
     * Get province
     *
     * @return string
     */
    public function getProvince() {
        return $this->province;
    }

    /**
     *
     * Set province
     *
     * @param string $province province
     * @return LookingFor
     */
    public function setProvince($province) {
        $this->province = $province;
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
     *
     * Set city
     *
     * @param string $city city
     * @return LookingFor
     */
    public function setCity($city) {
        $this->city = $city;
        return $this;
    }

    /**
     *
     * Get squereForm
     *
     * @return string
     */
    public function getSquereFrom() {
        return $this->squereFrom;
    }

    /**
     *
     * Set squereFrom
     *
     * @param string $squereFrom squere from
     * @return LookingFor
     */
    public function setSquereFrom($squereFrom) {
        $this->squereFrom = $squereFrom;
        return $this;
    }
    /**
     *
     * Get squere to
     *
     * @return string
     */
    public function getSquereTo() {
        return $this->squereTo;
    }

    /**
     *
     * Set squereTo
     *
     * @param string $squereTo squere to
     * @return LookingFor
     */
    public function setSquereTo($squereTo) {
        $this->squereTo = $squereTo;
        return $this;
    }

    /**
     *
     * Get roomsForm
     *
     * @return string
     */
    public function getRoomsFrom() {
        return $this->roomsFrom;
    }

    /**
     *
     * Set roomsFrom
     *
     * @param string $roomsFrom  rooms form
     * @return LookingFor
     */
    public function setRoomsFrom($roomsFrom) {
        $this->roomsFrom = $roomsFrom;
        return $this;
    }

    /**
     *
     * Get roomsTo
     *
     * @return string
     */
    public function getRoomsTo() {
        return $this->roomsTo;
    }

    /**
     *
     * Set roomsTo
     *
     * @param string $roomsTo rooms to
     * @return LookingFor
     */
    public function setRoomsTo($roomsTo) {
        $this->roomsTo = $roomsTo;
        return $this;
    }

    /**
     *
     * Get floorForm
     *
     * @return string
     */
    public function getFloorFrom() {
        return $this->floorFrom;
    }

    /**
     *
     * Set floorFrom
     *
     * @param string $floorFrom floor from
     * @return LookingFor
     */
    public function setFloorFrom($floorFrom) {
        $this->floorFrom = $floorFrom;
        return $this;
    }

    /**
     *
     * Get floorTo
     *
     * @return string
     */
    public function getFloorTo() {
        return $this->floorTo;
    }

    /**
     *
     * Set floorTo
     *
     * @param string $floorTo floor to
     * @return LookingFor
     */
    public function setFloorTo($floorTo) {
        $this->floorTo = $floorTo;
        return $this;
    }

    /**
     *
     * Get priceFrom
     *
     * @return string
     */
    public function getPriceFrom() {
        return $this->priceFrom;
    }

    /**
     *
     * Set priceFrom
     *
     * @param string $priceFrom price from
     * @return LookingFor
     */
    public function setPriceFrom($priceFrom) {
        $this->priceFrom = $priceFrom;
        return $this;
    }

    /**
     *
     * Get priceTo
     *
     * @return string
     */
    public function getPriceTo() {
        return $this->priceTo;
    }

    /**
     *
     * Set priceTo
     *
     * @param string $priceTo price to
     * @return LookingFor
     */
    public function setPriceTo($priceTo) {
        $this->priceTo = $priceTo;
        return $this;
    }

    /**
     *
     * Get currency
     *
     * @return string
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     *
     * Set currency
     *
     * @param string $currency currency
     * @return LookingFor
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
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
     * @return LookingFor
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
     *
     * Set name
     *
     * @param string $name name
     * @return LookingFor
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     *
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
     * @return LookingFor
     */
    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }

    /**
     *
     * Get person country
     *
     * @return string
     */
    public function getPersonCountry() {
        return $this->personCountry;
    }

    /**
     *
     * Set personCountry
     *
     * @param string $personCountry person country
     * @return LookingFor
     */
    public function setPersonCountry($personCountry) {
        $this->personCountry = $personCountry;
        return $this;
    }

    /**
     *
     * Get person city
     *
     * @return string
     */
    public function getPersonCity() {
        return $this->personCity;
    }

    /**
     *
     * Set personCity
     *
     * @param string $personCity person city
     * @return LookingFor
     */
    public function setPersonCity($personCity) {
        $this->personCity = $personCity;
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
     * @return LookingFor
     */
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }
    /**
     *
     * Get person street
     *
     * @return string
     */
    public function getPersonStreet() {
        return $this->personStreet;
    }
    /**
     *
     * Set personStreet
     *
     * @param string $personStreet person street
     * @return LookingFor
     */
    public function setPersonStreet($personStreet) {
        $this->personStreet = $personStreet;
        return $this;
    }
}

