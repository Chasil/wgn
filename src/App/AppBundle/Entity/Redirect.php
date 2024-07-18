<?php

namespace App\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Redirect
 *
 * @ORM\Table(name="redirect")
 * @ORM\Entity(repositoryClass="App\AppBundle\Repository\RedirectRepository")
 */
class Redirect
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="fromUrl", type="string", length=255)
     */
    private $fromUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="toUrl", type="string", length=255)
     */
    private $toUrl;

    /**
     * @var bool
     *
     * @ORM\Column(name="withParams", type="boolean")
     */
    private $withParams;

    /**
     * @var bool
     *
     * @ORM\Column(name="isPermanent", type="boolean")
     */
    private $isPermanent;

    /**
     * @var bool
     *
     * @ORM\Column(name="isFull", type="boolean")
     */
    private $isFull;


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
     * Set fromUrl
     *
     * @param string $fromUrl
     *
     * @return Redirect
     */
    public function setFromUrl($fromUrl)
    {
        $this->fromUrl = $fromUrl;

        return $this;
    }

    /**
     * Get fromUrl
     *
     * @return string
     */
    public function getFromUrl()
    {
        return $this->fromUrl;
    }

    /**
     * Set toUrl
     *
     * @param string $toUrl
     *
     * @return Redirect
     */
    public function setToUrl($toUrl)
    {
        $this->toUrl = $toUrl;

        return $this;
    }

    /**
     * Get toUrl
     *
     * @return string
     */
    public function getToUrl()
    {
        return $this->toUrl;
    }

    /**
     * Set withParams
     *
     * @param boolean $withParams
     *
     * @return Redirect
     */
    public function setWithParams($withParams)
    {
        $this->withParams = $withParams;

        return $this;
    }

    /**
     * Get withParams
     *
     * @return bool
     */
    public function getWithParams()
    {
        return $this->withParams;
    }

    /**
     * Set isPermanent
     *
     * @param boolean $isPermanent
     *
     * @return Redirect
     */
    public function setIsPermanent($isPermanent)
    {
        $this->isPermanent = $isPermanent;

        return $this;
    }

    /**
     * Get isPermanent
     *
     * @return bool
     */
    public function isPermanent()
    {
        return $this->isPermanent;
    }

    /**
     * @return bool
     */
    public function isFull()
    {
        return $this->isFull;
    }

    /**
     * @param bool $isFull
     * @return Redirect
     */
    public function setIsFull(bool $isFull)
    {
        $this->isFull = $isFull;

        return $this;
    }

}

