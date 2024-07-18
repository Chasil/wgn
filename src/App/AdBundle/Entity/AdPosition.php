<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AdPosition
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="ad_position")
 * @ORM\Entity(repositoryClass="App\AdBundle\Entity\AdPositionRepository")
 */
class AdPosition
{
    /**
     * @const mode random
     */
    const MODE_RANDOM = 0;
    /**
     * @const mode ordering
     */
    const MODE_ORDERING = 1;
    /**
     * @var int ad position id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string ad position name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string ad position uniqKey
     *
     * @ORM\Column(name="uniqKey", type="string", length=255, unique=true)
     */
    private $uniqKey;

    /**
     * @var string ad position description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int ad position mode
     *
     * @ORM\Column(name="mode", type="integer")
     */
    private $mode;
    /**
     * @var int ad position adsLimit
     *
     * @ORM\Column(name="adsLimit", type="integer")
     */
    private $adsLimit;
    /**
     * @var bool ad position isPublish
     *
     * @ORM\Column(name="isPublish", type="boolean")
     */
    private $isPublish;
    /**
     * @var bool ad position isPublish
     *
     * @ORM\Column(name="isMobileSupported", type="boolean")
     */
    private $isMobileSupported;
    /**
     * @var bool ad isOfficePosition
     *
     * @ORM\Column(name="isOfficePosition", type="boolean",nullable=true)
     */
    private $isOfficePosition;
    /**
     * @var int ad numberOfDisplay
     *
     * @ORM\Column(name="numberOfDisplay", type="integer",nullable=true)
     */
    private $numberOfDisplay;
    /**
     * @var Ad[] collection of ads
     *
     * @ORM\OneToMany(targetEntity="Ad", mappedBy="position")
     * @ORM\OrderBy({"ordering" = "DESC"})
     */
    protected $ads;

    /**
     * Constructor
     */
    public function __construct() {
        $this->ads = new ArrayCollection();
        $this->isPublish = true;
        $this->isOfficePosition = false;
    }

    /**
     * Ad position to string
     *
     * @return string
     */
    public function __toString() {
        return $this->name;
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
     * @param string $name
     *
     * @return AdPosition
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
     * Set uniqKey
     *
     * @param string $uniqKey
     *
     * @return AdPosition
     */
    public function setUniqKey($uniqKey)
    {
        $this->uniqKey = $uniqKey;

        return $this;
    }

    /**
     * Get uniqKey
     *
     * @return string
     */
    public function getUniqKey()
    {
        return $this->uniqKey;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return AdPosition
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set mode
     *
     * @param integer $mode
     *
     * @return AdPosition
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Get mode
     *
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set isPublish
     *
     * @param boolean $isPublish
     *
     * @return AdPosition
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
     * Get numberOfDisplay
     *
     * @return integer
     */
    public function getNumberOfDisplay() {
        return $this->numberOfDisplay;
    }

    /**
     *
     * Set numberOfDisplay
     *
     * @param int $numberOfDisplay
     * @return AdPosition
     */
    public function setNumberOfDisplay($numberOfDisplay) {
        $this->numberOfDisplay = $numberOfDisplay;
        return $this;
    }

    /**
     *
     * Get adsLimit
     *
     * @return int
     */
    public function getAdsLimit() {
        return $this->adsLimit;
    }
    /**
     * Set adsLimit
     *
     *
     * @param int $adsLimit
     * @return AdPosition
     */
    public function setAdsLimit($adsLimit) {
        $this->adsLimit = $adsLimit;
        return $this;
    }
    /**
     *
     * Get ads
     *
     * @return Ad[]
     */
    public function getAds() {
        return $this->ads;
    }
    /**
     *
     * Set ads
     *
     * @param Ad[] $ads
     * @return AdPosition
     */
    public function setAds($ads) {
        $this->ads = $ads;
        return $this;
    }

    /**
     *
     * Check if position has ads
     *
     * @return bool
     */
    public function hasAds(){
       return (count($this->getAds())>0) ? true : false;
    }

    /**
     * Get isOfficePosition
     *
     * @return bool
     */
    public function getIsOfficePosition() {
        return $this->isOfficePosition;
    }

    /**
     * Set isOfficePosition
     *
     * @param bool $isOfficePosition
     * @return AdPosition
     */
    public function setIsOfficePosition($isOfficePosition) {
        $this->isOfficePosition = $isOfficePosition;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMobileSupported()
    {
        return $this->isMobileSupported;
    }

    /**
     * @param bool $isMobileSupported
     * @return AdPosition
     */
    public function setIsMobileSupported(bool $isMobileSupported)
    {
        $this->isMobileSupported = $isMobileSupported;

        return $this;
    }


}

