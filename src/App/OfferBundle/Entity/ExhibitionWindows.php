<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ExhibitionWindows
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="exhibition_windows")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\ExhibitionWindowsRepository")
 */
class ExhibitionWindows
{
    /**
     * @var int exhibition windows id
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
     * @var Offer[] collection of offers
     *
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="exhibitionWindows")
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
     * @return ExhibitionWindows
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
     * @return ExhibitionWindows
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
     * @return ExhibitionWindows
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
     * Get ordering
     *
     * @return int
     */
    function getOffers() {
        return $this->offers;
    }

    /**
     * Set offers
     *
     * @param Offer[] $offers offers
     *
     * @return ExhibitionWindows
     */
    function setOffers($offers) {
        $this->offers = $offers;
        return $this;
    }
    /**
     * Exhibition windows to string
     *
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
}

