<?php
// src/Acme/UserBundle/Entity/User.php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ReviewRating
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    /** @ORM\ManyToOne(targetEntity="Review", inversedBy="ratings") **/
    private $review;

    /** @ORM\ManyToOne(targetEntity="User", inversedBy="reviewRatings") **/
    private $user;

    public function __construct()
    {
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
     * Set value
     *
     * @param integer $value
     * @return ReviewRating
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set review
     *
     * @param \AppBundle\Entity\Review $review
     * @return ReviewRating
     */
    public function setReview(\AppBundle\Entity\Review $review = null)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return \AppBundle\Entity\Review
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return ReviewRating
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
