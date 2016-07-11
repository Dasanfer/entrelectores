<?php
// src/Acme/UserBundle/Entity/User.php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class BookList
{
    const USER_PRIVATE = 0;
    const READ_PUBLIC = 1;
    const EDIT_PUBLIC = 2;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"follows"})

     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $oldId;

    /**
     * @ORM\Column(length=128)
     * @JMS\Expose
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(length=10000, nullable=true)
     * @JMS\Expose
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="lists")
     **/
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="listsFollowed")
     **/
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity="Book", inversedBy="lists")
     * @ORM\JoinTable(name="lists_books")
     **/
    private $books;

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
     * @ORM\Column(type="integer")
     */
    private $publicFlag;

    /**
     * @ORM\Column(type="integer")
     */
    private $followersCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $bookCount;

    /**
    * @ORM\Column(type="boolean", nullable=true)
    */
    private $globalFollow;

    /** @ORM\OneToMany(targetEntity="Comment", mappedBy="list", cascade={"remove"}) **/
    private $comments;

    public function __construct()
    {
        $this->followersCount = 0;
        $this->bookCount = 0;

        $this->publicFlag = BookList::USER_PRIVATE;
        $this->books = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->followers = new ArrayCollection();
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

    /** @ORM\PreUpdate */
    public function setCounts()
    {
        $this->followersCount = $this->followers->count();
        $this->bookCount = $this->books->count();
    }

    public function isPublic(){
        return !is_null($this->publicFlag) && $this->publicFlag >= $this::READ_PUBLIC;
    }

    public function getGlobalFollow(){
        return $this->globalFollow;
    }

    public function setGlobalFollow($globalFollow){
        $this->globalFollow = $globalFollow;
        return $this;
    }

    /**
     * Set oldId
     *
     * @param integer $oldId
     * @return Book
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
     * Set text
     *
     * @param string $text
     * @return BookList
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
     * Set name
     *
     * @param string $name
     * @return BookList
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
     * Set created
     *
     * @param \DateTime $created
     * @return BookList
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
     * @return BookList
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return BookList
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
     * Add books
     *
     * @param \AppBundle\Entity\Book $books
     * @return BookList
     */
    public function addBook(\AppBundle\Entity\Book $books)
    {
        $this->books[] = $books;

        return $this;
    }

    /**
     * Remove books
     *
     * @param \AppBundle\Entity\Book $books
     */
    public function removeBook(\AppBundle\Entity\Book $books)
    {
        $this->books->removeElement($books);
    }

    /**
     * Get books
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * Set publicFlag
     *
     * @param integer $publicFlag
     * @return BookList
     */
    public function setPublicFlag($publicFlag)
    {
        $this->publicFlag = $publicFlag;

        return $this;
    }

    /**
     * Get publicFlag
     *
     * @return integer
     */
    public function getPublicFlag()
    {
        return $this->publicFlag;
    }

    /**
     * Add followers
     *
     * @param \AppBundle\Entity\User $followers
     * @return BookList
     */
    public function addFollower(\AppBundle\Entity\User $followers)
    {
        $this->followers[] = $followers;

        return $this;
    }

    /**
     * Remove followers
     *
     * @param \AppBundle\Entity\User $followers
     */
    public function removeFollower(\AppBundle\Entity\User $followers)
    {
        $this->followers->removeElement($followers);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Add comments
     *
     * @param \AppBundle\Entity\Comment $comments
     * @return BookList
     */
    public function addComment(\AppBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \AppBundle\Entity\Comment $comments
     */
    public function removeComment(\AppBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return BookList
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
     * Set followersCount
     *
     * @param integer $followersCount
     * @return BookList
     */
    public function setFollowersCount($followersCount)
    {
        $this->followersCount = $followersCount;

        return $this;
    }

    /**
     * Get followersCount
     *
     * @return integer
     */
    public function getFollowersCount()
    {
        return $this->followersCount;
    }

    /**
     * Set bookCount
     *
     * @param integer $bookCount
     * @return BookList
     */
    public function setBookCount($bookCount)
    {
        $this->bookCount = $bookCount;

        return $this;
    }

    /**
     * Get bookCount
     *
     * @return integer
     */
    public function getBookCount()
    {
        return $this->bookCount;
    }
}
