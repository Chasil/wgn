<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AdditionalServiceType
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="additional_service_type")
 * @ORM\Entity(repositoryClass="App\OfficeBundle\Entity\AdditionalServiceTypeRepository")
 */
class AdditionalServiceType
{
    /**
     * @var int additional service type id
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
     * @var int ordering
     *
     * @ORM\Column(name="ordering", type="integer")
     */
    private $ordering;
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
     * @return AdditionalService
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
     * Additional service type to string
     *
     * @return strong
     */
    public function __toString() {
        return $this->name;
    }

    /**
     * Get ordering
     * @return int
     */
    public function getOrdering() {
        return $this->ordering;
    }

    /**
     * Set ordering
     * @param int $ordering ordering
     * @return AdditionalServiceType
     */
    public function setOrdering($ordering) {
        $this->ordering = $ordering;
        return $this;
    }
}

