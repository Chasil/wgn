<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CompanyData
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="company_data")
 * @ORM\Entity(repositoryClass="App\UserBundle\Entity\CompanyDataRepository")
 */
class CompanyData
{
    /**
     * @var int company data id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string nip
     *
     * @ORM\Column(name="nip", type="string", length=20, nullable=true)
     */
    private $nip;

    /**
     * @var string regon
     *
     * @ORM\Column(name="regon", type="string", length=255, nullable=true)
     */
    private $regon;

    /**
     * @var string krs
     *
     * @ORM\Column(name="krs", type="string", length=20, nullable=true)
     */
    private $krs;
    /**
     * @var bool is billing address
     *
     * @ORM\Column(name="isBilling", type="boolean")
     */
    private $isBilling;
    /**
     * @var Address address
     *
     * @ORM\OneToOne(targetEntity="Address",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    private $address;
    /**
     * @var User user[] collection of users
     *
     * @ORM\OneToMany(targetEntity="App\UserBundle\Entity\User", mappedBy="companyData")
     */
    protected $users;

    /**
     * Constructor
     */
    public function __construct() {
        $this->isBilling = true;
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
     * @return CompanyData
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
     * Set nip
     *
     * @param string $nip nip
     *
     * @return CompanyData
     */
    public function setNip($nip)
    {
        $this->nip = $nip;

        return $this;
    }

    /**
     * Get nip
     *
     * @return string
     */
    public function getNip()
    {
        return $this->nip;
    }

    /**
     * Set regon
     *
     * @param string $regon regon
     *
     * @return CompanyData
     */
    public function setRegon($regon)
    {
        $this->regon = $regon;

        return $this;
    }

    /**
     * Get regon
     *
     * @return string
     */
    public function getRegon()
    {
        return $this->regon;
    }

    /**
     * Set krs
     *
     * @param string $krs krs
     *
     * @return CompanyData
     */
    public function setKrs($krs)
    {
        $this->krs = $krs;

        return $this;
    }

    /**
     * Get krs
     *
     * @return string
     */
    public function getKrs()
    {
        return $this->krs;
    }

    /**
     * Get isBilling
     *
     * @return boolean
     */
    public function getIsBilling() {
        return $this->isBilling;
    }

    /**
     * Set isBilling
     *
     * @param boolean $isBilling is billing address
     *
     * @return CompanyData
     */
    public function setIsBilling($isBilling) {
        $this->isBilling = $isBilling;
        return $this;
    }

    /**
     * Get address
     *
     * @return Address
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param Address $address address
     *
     * @return CompanyData
     */
    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }


}

