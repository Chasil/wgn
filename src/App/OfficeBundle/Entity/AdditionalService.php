<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AdditionalService
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="additional_service")
 * @ORM\Entity(repositoryClass="App\OfficeBundle\Entity\AdditionalServiceRepository")
 */
class AdditionalService
{
    /**
     * @var int additional service id
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
     * @var string url
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var Office office
     *
     * @ORM\ManyToOne(targetEntity="Office", inversedBy="additionalServices")
     */
    protected $office;
    /**
     * @var AdditionalServiceType additional service type
     * @ORM\ManyToOne(targetEntity="AdditionalServiceType", inversedBy="additionalService")
     */
    protected $type;

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
     * Set url
     *
     * @param string $url url
     *
     * @return AdditionalService
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set office
     *
     * @param integer $office office
     *
     * @return AdditionalService
     */
    public function setOffice($office)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * Get office
     *
     * @return int
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     *  Get type
     * @return AdditionalServiceType
     */
    public function getType() {
        return $this->type;
    }
    /**
     * Set AdditionalServiceType
     * @param AdditionalServiceType $type additional service type
     * @return AdditionalService
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }
}

