<?php
/**
 * This file is part of the AppAppBundle package.
 *
 */
namespace App\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BlockPlace
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="block_place")
 * @ORM\Entity(repositoryClass="App\AppBundle\Entity\BlockPlaceRepository")
 */
class BlockPlace
{
    /**
     * @var int block place id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string block place name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    /**
     * @var string block place unique key
     *
     * @ORM\Column(name="uniqueKey", type="string", length=255,unique=true)
     */
    private $uniqueKey;
    /**
     * @var BlockElement[] collection of block elements
     *
     * @ORM\OneToMany(targetEntity="BlockElement", mappedBy="place")
     */
    protected $elements;

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
     * @param string $name block place name
     *
     * @return BoxPlace
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
     * Get uniqueKey
     *
     * @return string
     */
    public function getUniqueKey() {
        return $this->uniqueKey;
    }

    /**
     * Set uniqueKey
     *
     * @param string $uniqueKey
     * @return BlockPlace
     */
    public function setUniqueKey($uniqueKey) {
        $this->uniqueKey = $uniqueKey;
        return $this;
    }

    /**
     * Get Elements
     * 
     * @return BlockElement[]
     */
    public function getElements() {
        return $this->elements;
    }

    /**
     * Set elements
     *
     * @param BlockElement[] $elements
     * @return BlockPlace
     */
    public function setElements($elements) {
        $this->elements = $elements;
        return $this;
    }

    /**
     * Block place to string
     * @return string
     */
    public function __toString() {
        return $this->name . '('.$this->uniqueKey.')';
    }
}

