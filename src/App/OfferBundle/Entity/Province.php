<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Province
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="province")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\ProvinceRepository")
 */
class Province
{
    /**
     * @var int province id
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
     * @var Country country
     *
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="provinces")
     */
    protected $country;

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
     * @return Province
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
     * Get country
     *
     * @return Country
     */
    function getCountry() {
        return $this->country;
    }

    /**
     * Set country
     * @param Country $country country
     * @return Province
     */
    function setCountry($country) {
        $this->country = $country;
        return $this;
    }

}

