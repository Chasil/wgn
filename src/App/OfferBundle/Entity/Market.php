<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Market
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="market")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\MarketRepository")
 */
class Market
{
    /**
     * @var int market id
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
     * @var Offer[] collection of offers
     *
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="market")
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
     * @return Market
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
     * @return Market
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
     * Get offers
     *
     * @return Offer[]
     */
    public function getOffers() {
        return $this->offers;
    }
    /**
     * Set offers
     *
     * @param Offer[] $offers collection of offers
     * @return Market
     */
    public function setOffers($offers) {
        $this->offers = $offers;
        return $this;
    }
    /**
     * Market to string
     * 
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
}

