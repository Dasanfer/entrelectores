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
 */
class BookUserRelation
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /** @ORM\ManyToOne(targetEntity="Book", inversedBy="userRelations")
     * @Assert\NotNull()
     **/
    private $book;

    /** @ORM\ManyToOne(targetEntity="User", inversedBy="bookRelations")
     *
     * **/
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $beginRead;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endRead;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $want;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set beginRead
     *
     * @param \DateTime $beginRead
     * @return BookUserRelation
     */
    public function setBeginRead($beginRead)
    {
        $this->beginRead = $beginRead;

        return $this;
    }

    /**
     * Get beginRead
     *
     * @return \DateTime 
     */
    public function getBeginRead()
    {
        return $this->beginRead;
    }

    /**
     * Set endRead
     *
     * @param \DateTime $endRead
     * @return BookUserRelation
     */
    public function setEndRead($endRead)
    {
        $this->endRead = $endRead;

        return $this;
    }

    /**
     * Get endRead
     *
     * @return \DateTime 
     */
    public function getEndRead()
    {
        return $this->endRead;
    }

    /**
     * Set want
     *
     * @param \DateTime $want
     * @return BookUserRelation
     */
    public function setWant($want)
    {
        $this->want = $want;

        return $this;
    }

    /**
     * Get want
     *
     * @return \DateTime 
     */
    public function getWant()
    {
        return $this->want;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return BookUserRelation
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
     * @return BookUserRelation
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
     * Set book
     *
     * @param \AppBundle\Entity\Book $book
     * @return BookUserRelation
     */
    public function setBook(\AppBundle\Entity\Book $book = null)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return \AppBundle\Entity\Book 
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return BookUserRelation
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
