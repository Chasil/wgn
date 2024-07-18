<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Neighborhood
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="neighborhood")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\NeighborhoodRepository")
 */
class Neighborhood
{
    /**
     * @var int neighborhood id
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
     * @var Category[] collection of categories
     *
     * @ORM\ManyToMany(targetEntity="Category", mappedBy="neighborhood")
     */
    private $categories;

    /**
     * Constructor
     */
    function __construct() {
        $this->categories = new ArrayCollection();
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
     * @return Neighborhood
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
     * Get categories
     *
     * @return Cateogry[]
     */
    function getCategories() {
        return $this->categories;
    }
    /**
     * Set categories
     *
     * @param  Cateogry[] $categories collection of $categories
     *
     * @return Neighborhood
     */
    function setCategories($categories) {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Set isPublish
     *
     * @param boolean $isPublish publication state
     *
     * @return Neighborhood
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
     * Neighborhood to string
     * 
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
}

