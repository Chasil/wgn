<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Session
 *
 * @author wojciech przygoda
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\UserBundle\Entity\SessionRepository")
 */
class Session
{
    /**
     * @var string session id
     *
     * @ORM\Column(name="id",type="string", length=255)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string value
     *
     * @ORM\Column(name="value", type="text")
     */
    private $value;

    /**
     * @var integer time
     *
     * @ORM\Column(name="time", type="integer")
     */
    private $time;

    /**
     * @var integer life time
     *
     * @ORM\Column(name="lifetime", type="integer")
     */
    private $lifetime;
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param string $value value
     * @return Session
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set time
     *
     * @param integer $time time
     * @return Session
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return integer
     */
    public function getTime()
    {
        return $this->time;
    }
    /**
     * Get life time
     *
     * @return int
     */
    public function getLifetime() {
        return $this->lifetime;
    }

    /**
     * Set lifetime
     * @param int $lifetime life time
     */
    public function setLifetime($lifetime) {
        $this->lifetime = $lifetime;
    }


}
