<?php
// src/Acme/UserBundle/Entity/User.php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class PostCategory
{
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
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(length=256, nullable=true)
     */
    private $oldSlug;
    
    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="category")
     **/
    private $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
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
     * Set oldId
     *
     * @param integer $oldId
     * @return Genre
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
     * Set name
     *
     * @param string $name
     * @return Genre
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
     * Set slug
     *
     * @param string $slug
     * @return Genre
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set oldSlug
     *
     * @param string $oldSlug
     * @return Genre
     */
    public function setOldSlug($oldSlug)
    {
        $this->oldSlug = $oldSlug;

        return $this;
    }

    /**
     * Get oldSlug
     *
     * @return string 
     */
    public function getOldSlug()
    {
        return $this->oldSlug;
    }
    
    /**
     * Add books
     *
     * @param \AppBundle\Entity\Post $posts
     * @return Author
     */
    public function addPost(\AppBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove books
     *
     * @param \AppBundle\Entity\Post $posts
     */
    public function removePost(\AppBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get books
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    public function setPosts($posts) {
        $this->posts = $posts;
    }

        
    public function __toString() {
        return $this->name;
    }
    
    

}
