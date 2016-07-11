<?php
// src/Acme/UserBundle/Entity/User.php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class FollowEvent
{
    const book_add = 'book_add';
    const book_reading = 'book_reading';
    const book_read = 'book_read';
    const book_want = 'book_want';

    const comment = 'comment';
    const list_comment = 'list_comment';
    const author_comment = 'author_comment';

    const rating = 'rating';
    const review = 'review';

    const list_create = 'list_create';

    const follow_book = 'follow_book';
    const follow_list = 'follow_list';
    const follow_author = 'follow_author';
    const follow_user = 'follow_user';

    const status = 'status';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity="Book")
     **/
    private $book;

    /**
     * @ORM\ManyToOne(targetEntity="BookList")
     **/
    private $list;

    /**
     * @ORM\ManyToOne(targetEntity="Author")
     **/
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Comment")
     **/
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="Review")
     **/
    private $review;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     **/
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="UserStatus")
     **/
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     **/
    private $createdBy;

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

    public function __construct(){
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
     * Set type
     *
     * @param string $type
     * @return FollowEvent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return FollowEvent
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
     * @return FollowEvent
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
     * @return FollowEvent
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
     * @return FollowEvent
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
     * @return FollowEvent
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return FollowEvent
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
     * Set createdBy
     *
     * @param \AppBundle\Entity\User $createdBy
     * @return FollowEvent
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
     * Set comment
     *
     * @param \AppBundle\Entity\Comment $comment
     * @return FollowEvent
     */
    public function setComment(\AppBundle\Entity\Comment $comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return \AppBundle\Entity\Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set review
     *
     * @param \AppBundle\Entity\Review $review
     * @return FollowEvent
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
     * Set status
     *
     * @param \AppBundle\Entity\UserStatus $status
     * @return FollowEvent
     */
    public function setStatus(\AppBundle\Entity\UserStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \AppBundle\Entity\UserStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }
}
