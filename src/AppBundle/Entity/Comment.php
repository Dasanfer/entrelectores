<?php
// src/Acme/UserBundle/Entity/User.php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Expose
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="comments")
     **/
    private $book;

    /**
     * @ORM\ManyToOne(targetEntity="BookList", inversedBy="comments")
     **/
    private $list;

    /**
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="comments")
     **/
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     **/
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="children")
     **/
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="parent")
     **/
    private $children;

    /**
     * @ORM\Column(type="string", length=1024)
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    protected $content;

    public function __construct(){
        $this->children = new ArrayCollection();
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
     * Set created
     *
     * @param \DateTime $created
     * @return Comment
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
     * @return Comment
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
     * Set content
     *
     * @param string $content
     * @return Comment
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
     * Set book
     *
     * @param \AppBundle\Entity\Book $book
     * @return Comment
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
     * Set list
     *
     * @param \AppBundle\Entity\BookList $list
     * @return Comment
     */
    public function setList(\AppBundle\Entity\BookList $list = null)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * Get list
     *
     * @return \AppBundle\Entity\BookList
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\Author $author
     * @return Comment
     */
    public function setAuthor(\AppBundle\Entity\Author $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\User $createdBy
     * @return Comment
     */
    public function setCreatedBy(\AppBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \AppBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\Comment $parent
     * @return Comment
     */
    public function setParent(\AppBundle\Entity\Comment $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Comment
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \AppBundle\Entity\Comment $children
     * @return Comment
     */
    public function addChild(\AppBundle\Entity\Comment $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \AppBundle\Entity\Comment $children
     */
    public function removeChild(\AppBundle\Entity\Comment $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }
}
