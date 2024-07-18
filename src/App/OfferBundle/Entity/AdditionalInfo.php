<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AdditionalInfo
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="additional_info")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\AdditionalInfoRepository")
 */
class AdditionalInfo
{
    /**
     * @var int additional info id
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
     * @var Category category
     *
     * @ORM\ManyToMany(targetEntity="Category", mappedBy="additionalInfo")
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
     * @return AdditionalInfo
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
     * @return AdditionalInfo
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
     * Get categories
     *
     * @return Category[] collection of categories
     */
    function getCategories() {
        return $this->categories;
    }
    /**
     * Set categories
     *
     * @param Category[] $categories
     * @return AdditionalInfo
     */
    function setCategories($categories) {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Additional info to string
     * 
     * @return AdditionalInfo
     */
    public function __toString() {
        return $this->name;
    }
}

