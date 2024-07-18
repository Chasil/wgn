<?php
/**
 * This file is part of the AppAppBundle package.
 *
 */
namespace App\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BlockElement
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="block_element")
 * @ORM\Entity(repositoryClass="App\AppBundle\Entity\BlockElementRepository")
 */
class BlockElement
{
    /**
     * @var int block element id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string block element name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    /**
     * @var string block element name
     *
     * @ORM\Column(name="headerBgColor", type="string", length=255,nullable=true)
     */
    private $headerBgColor;
    /**
     * @var string block element header background color
     *
     * @ORM\Column(name="headerFontColor", type="string", length=255,nullable=true)
     */
    private $headerFontColor;
    /**
     * @var string block element header background color
     *
     * @ORM\Column(name="headerFontSize", type="integer",nullable=true)
     */
    private $headerFontSize;
    /**
     * @var string block element header background color
     *
     * @ORM\Column(name="headerFontIsBold", type="boolean" ,nullable=true)
     */
    private $headerFontIsBold;
    /**
     * @var string block element font color
     *
     * @ORM\Column(name="fontColor", type="string", length=255,nullable=true)
     */
    private $fontColor;

    /**
     * @var string block element font size
     *
     * @ORM\Column(name="fontSize", type="integer",nullable=true)
     */
    private $fontSize;
    /**
     * @var string block element font size
     *
     * @ORM\Column(name="fontIsBold", type="boolean", nullable=true)
     */
    private $fontIsBold;
    /**
     * @var string block element font color
     *
     * @ORM\Column(name="customStyle", type="string", length=255,nullable=true)
     */
    private $customStyle;
    /**
     * @var string block element service
     *
     * @ORM\Column(name="service", type="string", length=255)
     */
    private $service;

    /**
     * @var bool block element publication state
     *
     * @ORM\Column(name="isPublish", type="boolean")
     */
    private $isPublish;

    /**
     * @var int block element ordering
     *
     * @ORM\Column(name="ordering", type="integer")
     */
    private $ordering;

    /**
     * @var array  block element config
     *
     * @ORM\Column(name="config", type="array", nullable=true)
     */
    private $config;

    /**
     * @var BlockPlace block element place
     *
     * @ORM\ManyToOne(targetEntity="BlockPlace", inversedBy="elements")
     */
    protected $place;

    /**
     * @var array $changes list of changes
     */
    private $changes = array();

    /**
     * Constructor
     *
     */
    function __construct() {
        $this->config = array();
        $this->isPublish = true;
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
     * @param string $name block element name
     *
     * @return BoxElement
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
     * Set service
     *
     * @param string $service block element service
     *
     * @return BoxElement
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set isPublish
     *
     * @param boolean $isPublish block element publication state
     *
     * @return BoxElement
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
     * @param integer $ordering  block element ordering
     *
     * @return BoxElement
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
     * Set config
     *
     * @param string $config block element config
     *
     * @return BoxElement
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return string
     */
    public function getConfig()
    {
        return $this->config;
    }
    public function getHeaderBgColor() {
        return $this->headerBgColor;
    }

    public function setHeaderBgColor($headerBgColor) {
        $this->headerBgColor = $headerBgColor;
        return $this;
    }

    public function getHeaderFontColor() {
        return $this->headerFontColor;
    }

    public function setHeaderFontColor($headerFontColor) {
        $this->headerFontColor = $headerFontColor;
        return $this;
    }

    public function getFontColor() {
        return $this->fontColor;
    }

    public function setFontColor($fontColor) {
        $this->fontColor = $fontColor;
        return $this;
    }

    public function getFontSize() {
        return $this->fontSize;
    }

    public function setFontSize($fontSize) {
        $this->fontSize = $fontSize;
        return $this;
    }
    public function getHeaderFontSize() {
        return $this->headerFontSize;
    }

    public function setHeaderFontSize($headerFontSize) {
        $this->headerFontSize = $headerFontSize;
        return $this;
    }

    public function getCustomStyle() {
        return $this->customStyle;
    }

    public function setCustomStyle($customStyle) {
        $this->customStyle = $customStyle;
        return $this;
    }

    public function getHeaderFontIsBold() {
        return $this->headerFontIsBold;
    }

    public function setHeaderFontIsBold($headerFontIsBold) {
        $this->headerFontIsBold = $headerFontIsBold;
        return $this;
    }
    public function getFontIsBold() {
        return $this->fontIsBold;
    }

    public function setFontIsBold($fontIsBold) {
        $this->fontIsBold = $fontIsBold;
        return $this;
    }
    /**
     * Get place
     *
     * @return BlockPlace
     */
    public function getPlace() {
        return $this->place;
    }
    /**
     * Set place
     *
     * @param BlockPlace $place block element config
     *
     * @return BoxElement
     */
    public function setPlace($place) {
        $this->changes['place'][] = $this->place;
        $this->changes['place'][] = $place;
        $this->place = $place;
        return $this;
    }
    /**
     * Increment ordering
     *
     * @return BlockElement
     */
    public function incrementOrdering(){
        $this->ordering++;
        return $this;
    }

    /**
     * Decrement ordering
     * @return BlockElement
     */
    public function decrementOrdering(){
        $this->ordering--;
        return $this;
    }

    /**
     * Check is values changed
     *
     * @param string $field
     * @return bool
     */
    public function isChange($field){
        return isset($this->changes[$field]);
    }

    /**
     * Get changes
     *
     * @return array
     */
    public function getChanges(){
        return $this->changes;
    }
}

