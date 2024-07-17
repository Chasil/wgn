<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Address
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="App\UserBundle\Entity\AddressRepository")
 */
class Address
{
    /**
     * @var int address id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var Country country
     *
     * @ORM\ManyToOne(targetEntity="App\OfferBundle\Entity\Country", inversedBy="addresses")
     */
    private $country;

    /**
     * @var string province
     *
     * @ORM\Column(name="province", type="string", length=255, nullable=true)
     */
    private $province;

    /**
     * @var string city
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string street
     *
     * @ORM\Column(name="street", type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * @var string zip code
     *
     * @ORM\Column(name="zipCode", type="string", length=6, nullable=true)
     */
    private $zipCode;
    /**
     * @var string phone
     *
     * @ORM\Column(name="phone", type="string", length=11, nullable=true)
     */
    private $phone;
    /**
     * @var string phone 2
     *
     * @ORM\Column(name="phone2", type="string", length=11, nullable=true)
     */
    private $phone2;
    /**
     * @var string email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;
    /**
     * @var string email 2
     *
     * @ORM\Column(name="email2", type="string", length=255, nullable=true)
     */
    private $email2;
    /**
     * @var string open from
     *
     * @ORM\Column(name="openFrom", type="string", length=20, nullable=true)
     */
    private $openFrom;
    /**
     * @var string open to
     *
     * @ORM\Column(name="openTo", type="string", length=20, nullable=true)
     */
    private $openTo;
    /**
     * @var bool is default
     *
     * @ORM\Column(name="isDefault", type="boolean")
     */
    private $isDefault;
   /**
    * @var User user
    *
    * @ORM\ManyToOne(targetEntity="User", inversedBy="addresses")
    */
    protected $user;
   /**
    * @var Office office
    *
    * @ORM\ManyToOne(targetEntity="App\OfficeBundle\Entity\Office", inversedBy="addresses")
    */
    protected $office;

    /**
     * Constructor
     */
    public function __construct() {
        $this->isDefault = true;
        $this->name = 'domyślny';
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name name
     *
     * @return Address
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set country
     *
     * @param Country $country country
     *
     * @return Address
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set province
     *
     * @param string $province province
     *
     * @return Address
     */
    public function setProvince($province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province
     *
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set city
     *
     * @param string $city city
     *
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set street
     *
     * @param string $street street
     *
     * @return Address
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode zip code
     *
     * @return Address
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set isDefault
     *
     * @param boolean $isDefault is default
     *
     * @return Address
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault
     *
     * @return bool
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }
    /**
     * Get user
     *
     * @return User
     */
    public function getUser() {
        return $this->user;
    }
    /**
     * Get user
     *
     * @return User
     */
    public function getOffice() {
        return $this->office;
    }
    /**
     * Set user
     *
     * @param boolean $user user
     *
     * @return Address
     */
    public function setUser($user) {
        $this->user = $user;
        return $this;
    }
    /**
     * Set office
     *
     * @param boolean $office office
     *
     * @return Address
     */
    public function setOffice($office) {
        $this->office = $office;
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
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
    /**
     * Set phone
     *
     * @param string $phone phone
     *
     * @return Address
     */
    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }
    /**
     * Set email
     *
     * @param string $email email
     *
     * @return Address
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }
    /**
     * Get openFrom
     *
     * @return string
     */
    public function getOpenFrom() {
        return $this->openFrom;
    }
    /**
     * Get openTo
     *
     * @return string
     */
    public function getOpenTo() {
        return $this->openTo;
    }
    /**
     * Set openFrom
     *
     * @param string $openFrom open from
     *
     * @return Address
     */
    public function setOpenForm($openFrom) {
        $this->openFrom = $openFrom;
        return $this;
    }
    /**
     * Set openTo
     *
     * @param string $openTo open to
     *
     * @return Address
     */
    public function setOpenTo($openTo) {
        $this->openTo = $openTo;
        return $this;
    }
    /**
     * Get phone2
     *
     * @return string
     */
    public function getPhone2() {
        return $this->phone2;
    }
    /**
     * Set phone2
     *
     * @param string $phone2 phone2
     *
     * @return Address
     */
    public function setPhone2($phone2) {
        $this->phone2 = $phone2;
        return $this;
    }
    /**
     * Get email2
     *
     * @return string
     */
    public function getEmail2() {
        return $this->email2;
    }
    /**
     * Set email2
     *
     * @param string $email2 email2
     *
     * @return Address
     */
    public function setEmail2($email2) {
        $this->email2 = $email2;
        return $this;
    }
    /**
     * Address to string
     *
     * @return string
     */
    public function __toString() {
        return $this->city . ' ' . $this->street;
    }
}

