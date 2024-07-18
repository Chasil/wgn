<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Message
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\MessageRepository")
 */
class Message
{
    /**
     * @var int message id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string subject
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     * @var string content
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime create date
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;
    /**
     * @var string phone
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;
    /**
     * @var string email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;
    /**
     * @var Offer offer
     * @ORM\ManyToOne(targetEntity="Offer", inversedBy="messages")
     */
    private $offer;
    /**
     * @var User sender
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="sendMessages")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $sender;

    /**
     * @var User recipient
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="messages")
     * @ORM\JoinColumn(name="recipient_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $recipient;

    /**
     * Constructor
     */
    public function __construct() {
        $this->createDate = new \DateTime();
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
     * Set subject
     *
     * @param string $subject subject
     *
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }
    /**
     * Get phone
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }
    /**
     * Set phone
     *
     * @param string $phone phone
     *
     * @return Message
     */
    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }
    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
    /**
     * Set email
     *
     * @param string $email email
     *
     * @return Message
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }
    /**
     * Set content
     *
     * @param string $content
     *
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set sender
     *
     * @param User $sender sender
     *
     * @return Message
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return User
     */
    public function getSender()
    {
        return $this->sender;
    }
    /**
     * Get recipient
     *
     * @return User
     */
    public function getRecipient() {
        return $this->recipient;
    }
    /**
     * Set recipient
     *
     * @param User $recipient recipient
     *
     * @return Message
     */
    public function setRecipient($recipient) {
        $this->recipient = $recipient;
        return $this;
    }
  /**
     * Get offer
     *
     * @return Offer
     */
    public function getOffer() {
        return $this->offer;
    }
    /**
     * Set offer
     *
     * @param Offer $offer offer
     *
     * @return Message
     */
    public function setOffer(Offer $offer) {
        $this->offer = $offer;
        return $this;
    }
    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate() {
        return $this->createDate;
    }
    /**
     * Set createDate
     *
     * @param \DateTime $createDate create date
     *
     * @return Message
     */
    public function setCreateDate(\DateTime $createDate) {
        $this->createDate = $createDate;
        return $this;
    }


}

