<?php
// src/Acme/UserBundle/Entity/User.php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Message
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /** @ORM\ManyToOne(targetEntity="User")
     * **/
    private $from;

    /** @ORM\ManyToOne(targetEntity="User")
     * **/
    private $to;

    /**
     * @ORM\Column(length=40)
     */
    private $uniqueid;

    /**
     * @ORM\Column(length=4096)
     * @Assert\NotBlank()
     */
    private $text;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var \DateTime $readed
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $readed;


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
     * Set text
     *
     * @param string $text
     * @return Review
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set unqueid
     *
     * @param string $text
     * @return Message
     */
    public function setUniqueid($uniqueid)
    {
        $this->uniqueid = $uniqueid;

        return $this;
    }

    /**
     * Get uniqueid
     *
     * @return string
     */
    public function getUniqueid()
    {
        return $this->uniqueid;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Review
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set readed
     *
     * @param \DateTime $readed
     * @return Review
     */
    public function setReaded($readed)
    {
        $this->readed = $readed;

        return $this;
    }

    /**
     * Get readed
     *
     * @return \DateTime
     */
    public function getReaded()
    {
        return $this->readed;
    }

    /**
     * Set from
     *
     * @param \AppBundle\Entity\User $user
     * @return Review
     */
    public function setFrom(\AppBundle\Entity\User $user = null)
    {
        $this->from = $user;

        return $this;
    }

    /**
     * Get from
     *
     * @return \AppBundle\Entity\User
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set from
     *
     * @param \AppBundle\Entity\User $user
     * @return Review
     */
    public function setTo(\AppBundle\Entity\User $user = null)
    {
        $this->to = $user;

        return $this;
    }

    /**
     * Get from
     *
     * @return \AppBundle\Entity\User
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @ORM\PrePersist
     */
    public function createUniqueid()
    {
      if ($this->to->getId() < $this->from->getId())
	$this->uniqueid = $this->to->getId().'-'.$this->from->getId();
      else
	$this->uniqueid = $this->from->getId().'-'.$this->to->getId();
    }
}
