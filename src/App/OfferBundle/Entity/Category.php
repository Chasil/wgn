<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * Class Category
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\CategoryRepository")
 */
class Category
{
    /**
     * @const type flat
     */
    const TYPE_FLAT = 'flat';
    /**
     * @const type house
     */
    const TYPE_HOUSE = 'house';
    /**
     * @const type plot
     */
    const TYPE_PLOT = 'plot';
    /**
     * @const type local
     */
    const TYPE_LOCAL = 'local';
    /**
     * @const type commercial
     */
    const TYPE_COMMERCIAL = 'commercial';
    /**
     * @const type garage
     */
    const TYPE_GARAGE = 'garage';
    /**
     * @var int category id
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
     * @var string unique key
     *
     * @ORM\Column(name="uniqueKey", type="string", length=255)
     */
    private $uniqueKey;
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
     * @var Type[] collection of types
     *
     * @ORM\OneToMany(targetEntity="Type", mappedBy="category")
     */
    protected $types;
    /**
     * @var Offer[] collection of offers
     *
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="category")
     */
    protected $offers;
    /**
     * @var Media[] collection of media
     *
     * @ORM\ManyToMany(targetEntity="Media", inversedBy="categories")
     * @ORM\JoinTable(name="category_media")
     */
    private $media;
    /**
     * @var AdditionalInfo[] collection of additional info
     *
     * @ORM\ManyToMany(targetEntity="AdditionalInfo", inversedBy="categories")
     * @ORM\JoinTable(name="category_additional_info")
     */
    private $additionalInfo;
    /**
     * @var Neighborhood[] collection of neighborhood
     *
     * @ORM\ManyToMany(targetEntity="Neighborhood", inversedBy="categories")
     * @ORM\JoinTable(name="category_neighborhood")
     */
    private $neighborhood;

    /**
     * Constructor
     */
    function __construct() {
        $this->types = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->additionalInfo = new ArrayCollection();
        $this->neighborhood = new ArrayCollection();
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
     * @return Category
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
     * @return Category
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
     * @return bool
     */
    public function getOrdering() {
        return $this->ordering;
    }

    /**
     * Set ordering
     *
     * @param boolean $ordering ordering
     *
     * @return Category
     */
    public function setOrdering($ordering) {
        $this->ordering = $ordering;
        return $this;
    }
    /**
     * Get types
     *
     * @return Type[]
     */
    public function getTypes() {
        return $this->types;
    }

    /**
     * Set types
     *
     * @param Type[] $types types
     *
     * @return Category
     */
    public function setTypes($types) {
        $this->types = $types;
        return $this;
    }
    /**
     * Get uniqueKey
     *
     * @return string
     */
    function getUniqueKey() {
        return $this->uniqueKey;
    }

    /**
     * Set uniqueKey
     *
     * @param string $uniqueKey unique key
     *
     * @return Category
     */
    function setUniqueKey($uniqueKey) {
        $this->uniqueKey = $uniqueKey;
        return $this;
    }
    /**
     * Get published types
     *
     * @return Type[]
     */
    public function getPublishedTypes(){
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("isPublish", true))
            ->orderBy(array("ordering" => Criteria::ASC))
        ;

        return $this->getTypes()->matching($criteria);
    }
    /**
     * Category to string
     * 
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
}

