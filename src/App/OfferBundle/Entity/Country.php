<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Country
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\CountryRepository")
 */
class Country
{
    /**
     * @var int country id
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
     * @var string iso code
     *
     * @ORM\Column(name="isoCode", type="string", length=3)
     */
    private $isoCode;
    /**
     * @var integer ordering
     *
     * @ORM\Column(name="ordering", type="integer",nullable=true)
     */
    private $ordering;
    /**
     * @var Offer[] offers
     *
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="country")
     */
    protected $offers;
    /**
     * @var Province[] provices
     * @ORM\OneToMany(targetEntity="Province", mappedBy="country")
     */
    protected $provinces;
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
     * @return Country
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
     * Set isoCode
     *
     * @param string $isoCode iso code
     *
     * @return Country
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * Get isoCode
     *
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Get ordering
     *
     * @return string
     */
    public function getOrdering() {
        return $this->ordering;
    }

    /**
     * Set ordering
     *
     * @param string $ordering ordering
     *
     * @return Country
     */
    public function setOrdering($ordering) {
        $this->ordering = $ordering;
        return $this;
    }

    /**
     * Get provinces
     *
     * @return Province[]
     */
    function getProvinces() {
        return $this->provinces;
    }

    /**
     * Set provinces
     *
     * @param string $provinces collection of provinces
     *
     * @return Country
     */
    function setProvinces($provinces) {
        $this->provinces = $provinces;
        return $this;
    }

    /**
     * Country to string
     *
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
}

