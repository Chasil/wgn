<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Property
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="property")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\PropertyRepository")
 */
class Property
{
    /**
     * @var int property id
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
     * @var bool publication state
     *
     * @ORM\Column(name="isPublish", type="boolean")
     */
    private $isPublish;

    /**
     * @var int ordering
     *
     * @ORM\Column(name="ordering", type="integer")
     */
    private $ordering;
    /**
     * @var Offer[] collectio of offers
     *
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="property")
     */
    protected $offers;

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
     * @return Property
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
     * Set isPublish
     *
     * @param boolean $isPublish publication state
     *
     * @return Property
     */
    public function setIsPublish($isPublish)
    {
        $this->isPublish = $isPublish;

        return $this;
    }

    /**
     * Get isPublish
     *
     * @return bool
     */
    public function getIsPublish()
    {
        return $this->isPublish;
    }

    /**
     * Set ordering
     *
     * @param integer $ordering ordering
     *
     * @return Property
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;

        return $this;
    }

    /**
     * Get ordering
     *
     * @return int
     */
    public function getOrdering()
    {
        return $this->ordering;
    }
    /**
     * Get offers
     *
     * @return Offer[]
     */
    function getOffers() {
        return $this->offers;
    }

    /**
     * Set offers
     *
     * @param Offer[] $offers collection of offers
     * @return Property
     */
    function setOffers($offers) {
        $this->offers = $offers;
        return $this;
    }
    /**
     * Property to string
     * 
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
}

