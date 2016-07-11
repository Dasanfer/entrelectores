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
class Review
{
    const FLAG_SPAM = -1;
    const FLAG_OFFENSIVE = -2;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $oldId;

    /**
     * @ORM\Column(length=256)
     * @Assert\NotBlank()
     *
     */
    private $title;

    /**
     * @ORM\Column(length=10000)
     * @Assert\NotBlank()
     */
    private $text;

    /** @ORM\ManyToOne(targetEntity="Book", inversedBy="reviews")
     * @Assert\NotNull()
     * **/
    private $book;

    /** @ORM\ManyToOne(targetEntity="User", inversedBy="reviews")
     * **/
    private $user;

    /** @ORM\OneToMany(targetEntity="ReviewRating", mappedBy="review") **/
    private $ratings;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\NotNull()
     */
    protected $spoiler;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $flag;

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

    /** @ORM\Column(type="float") **/
    private $cachedRate;

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
        $this->cachedRate = 0;
        // your own logic
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return Review
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
     * @return Review
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
     * Set ratings
     *
     * @param \AppBundle\Entity\ReviewRating $ratings
     * @return Review
     */
    public function setRatings(\AppBundle\Entity\ReviewRating $ratings = null)
    {
        $this->ratings = $ratings;

        return $this;
    }

    /**
     * Get ratings
     *
     * @return \AppBundle\Entity\ReviewRating
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * Set flag
     *
     * @param integer $flag
     * @return Review
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * Get flag
     *
     * @return integer
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Review
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set oldId
     *
     * @param integer $oldId
     * @return Review
     */
    public function setOldId($oldId)
    {
        $this->oldId = $oldId;

        return $this;
    }

    /**
     * Get oldId
     *
     * @return integer
     */
    public function getOldId()
    {
        return $this->oldId;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Review
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

    /**
     * Add ratings
     *
     * @param \AppBundle\Entity\ReviewRating $ratings
     * @return Review
     */
    public function addRating(\AppBundle\Entity\ReviewRating $ratings)
    {
        $this->ratings[] = $ratings;

        return $this;
    }

    /**
     * Remove ratings
     *
     * @param \AppBundle\Entity\ReviewRating $ratings
     */
    public function removeRating(\AppBundle\Entity\ReviewRating $ratings)
    {
        $this->ratings->removeElement($ratings);
    }

    public function __toString() {
        return $this->title." - ".$this->book->getTitle();
    }


    /**
     * Set spoiler
     *
     * @param boolean $spoiler
     * @return Review
     */
    public function setSpoiler($spoiler)
    {
        $this->spoiler = $spoiler;

        return $this;
    }

    /**
     * Get spoiler
     *
     * @return boolean
     */
    public function getSpoiler()
    {
        return $this->spoiler;
    }

    /**
     * Set cachedRate
     *
     * @param float $cachedRate
     * @return Review
     */
    public function setCachedRate($cachedRate)
    {
        $this->cachedRate = $cachedRate;

        return $this;
    }

    /**
     * Get cachedRate
     *
     * @return float
     */
    public function getCachedRate()
    {
        return $this->cachedRate;
    }
}
