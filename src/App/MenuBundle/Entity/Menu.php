<?php
/**
 * This file is part of the AppMenuBundle package.
 *
 */
namespace App\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Menu
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="menu")
 * @ORM\Entity(repositoryClass="App\MenuBundle\Entity\MenuRepository")
 */
class Menu
{
    /**
     * @var int menu id
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
     * @var string uniqueKey
     *
     * @ORM\Column(name="uniqueKey", type="string", length=255, unique=true)
     */
    private $uniqueKey;

    /**
     * @var bool isPublish
     *
     * @ORM\Column(name="isPublish", type="boolean")
     */
    private $isPublish;
    /**
     * @var MenuItem[] menu items
     *
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="menu")
     */
    protected $items;

    /**
     * Constructor
     */
    public function __construct() {
        $this->items = new ArrayCollection();
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
     * @return Menu
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set uniqueKey
     *
     * @param string $uniqueKey unique key
     *
     * @return Menu
     */
    public function setUniqueKey($uniqueKey)
    {
        $this->uniqueKey = $uniqueKey;

        return $this;
    }

    /**
     * Get uniqueKey
     *
     * @return string
     */
    public function getUniqueKey()
    {
        return $this->uniqueKey;
    }

    /**
     * Set isPublish
     *
     * @param boolean $isPublish isPublish
     *
     * @return Menu
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
     * Get items
     *
     * @return ArrayCollection
     */
    function getItems() {
        return $this->items;
    }
    /**
     * Set items
     *
     * @param ArrayCollection MenuItem collection of menu items
     *
     * @return Menu
     */
    function setItems($items) {
        $this->items = $items;
        return $this;
    }


}

