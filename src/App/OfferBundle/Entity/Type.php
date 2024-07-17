<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Type
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="type")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\TypeRepository")
 */
class Type
{
    /**
     * @var int type id
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
     * @var integer ordering
     *
     * @ORM\Column(name="ordering", type="integer")
     */
    private $ordering;
    /**
     * @var Category category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="types")
     */
    protected $category;
    /**
     * @var Offer[] collection of offers
     *
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="type")
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
     * @return Type
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
     * @return Type
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
     * Get ordering
     *
     * @return int
     */
    public function getOrdering() {
        return $this->ordering;
    }
    /**
     * Set ordering
     *
     * @param int $ordering ordering
     * @return Type
     */
    public function setOrdering($ordering) {
        $this->ordering = $ordering;
        return $this;
    }
    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory() {
        return $this->category;
    }
    /**
     * Set category
     *
     * @param Category $category category
     * @return Type
     */
    public function setCategory($category) {
        $this->category = $category;
        return $this;
    }
    /**
     * Type to string
     * 
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
}

