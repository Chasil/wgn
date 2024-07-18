<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TechnicalCondition
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="technical_condition")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\TechnicalConditionRepository")
 */
class TechnicalCondition
{
    /**
     * @var int technical condition id
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
     * @var Offers[] collection of offers
     *
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="technicalCondition")
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
     * @return TechnicalCondition
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
     * @return TechnicalCondition
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
     * @return TechnicalCondition
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
     * @return TechnicalCondition
     */
    function setOffers($offers) {
        $this->offers = $offers;
        return $this;
    }

    /**
     * Technical condition to string
     * 
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
}

