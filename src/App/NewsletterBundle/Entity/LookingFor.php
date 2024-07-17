<?php
/**
 * This file is part of the AppNewsletterBundle package.
 *
 */
namespace App\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class LookingFor
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="looking_for")
 * @ORM\Entity(repositoryClass="App\NewsletterBundle\Entity\LookingForRepository")
 */
class LookingFor
{
    /**
     * @var int looking for subscription id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string email
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var array query
     *
     * @ORM\Column(name="query", type="array")
     */
    private $query;

    /**
     * @var \DateTime create date
     *
     * @ORM\Column(name="createDate", type="datetime")
     */
    private $createDate;

    /**
     * @var string ip
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * @var bool is active
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;
    /**
     * @var string hash
     *
     * @ORM\Column(name="hash", type="string", length=255)
     */
    private $hash;

    /**
     * Constructor
     *
     */
    public function __construct() {
        $this->createDate = new \DateTime();
        $this->hash = sha1(uniqid(mt_rand(), true));
        $this->isActive = true;
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
     * Set email
     *
     * @param string $email email
     *
     * @return LookingFor
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set query
     *
     * @param array $query query
     *
     * @return LookingFor
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return array
     */
    public function getQuery()
    {
        foreach($this->query as $key=>$value){
            if($value==''){
                unset($this->query[$key]);
            }
        }

        return $this->query;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate create date
     *
     * @return LookingFor
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set ip
     *
     * @param string $ip ip address
     *
     * @return LookingFor
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive is active
     *
     * @return LookingFor
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    /**
     * Set hash
     *
     * @param string $hash hash
     *
     * @return Newsletter
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
}

