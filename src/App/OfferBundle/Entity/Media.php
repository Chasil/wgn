<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Media
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\MediaRepository")
 */
class Media
{
    /**
     * @var int media id
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
     * @ORM\ManyToMany(targetEntity="Category", mappedBy="media")
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
     * @return Media
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
     * @return Category[]
     */
    function getCategories() {
        return $this->categories;
    }
    /**
     * Set categories
     *
     * @param Category[] $categories collection of categories
     * @return Media
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
     * @return Media
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
     * Media to string
     * 
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
}

