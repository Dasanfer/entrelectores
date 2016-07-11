<?php
// src/Acme/UserBundle/Entity/User.php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class PollAnswer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $ipHash;

    /** @ORM\ManyToOne(targetEntity="PollOption", inversedBy="answers") **/
    private $option;

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

    public function __construct()
    {
    }

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
     * Set ipHash
     *
     * @param string $ipHash
     * @return PollAnswer
     */
    public function setIpHash($ipHash)
    {
        $this->ipHash = $ipHash;

        return $this;
    }

    /**
     * Get ipHash
     *
     * @return string 
     */
    public function getIpHash()
    {
        return $this->ipHash;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return PollAnswer
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return PollAnswer
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set option
     *
     * @param \AppBundle\Entity\PollOption $option
     * @return PollAnswer
     */
    public function setOption(\AppBundle\Entity\PollOption $option = null)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return \AppBundle\Entity\PollOption 
     */
    public function getOption()
    {
        return $this->option;
    }
}
