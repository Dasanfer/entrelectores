<?php
// src/Acme/UserBundle/Entity/User.php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use JMS\Serializer\Annotation as JMS;
/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="email", column=@ORM\Column(type="string", name="email", length=255, unique=false, nullable=false)),
 *      @ORM\AttributeOverride(name="emailCanonical", column=@ORM\Column(type="string", name="email_canonical", length=255, unique=false, nullable=true))
 *  })
 * @JMS\ExclusionPolicy("all")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 *  */
class User extends BaseUser
{

    const FEMALE = 'F';
    const MALE = 'M';

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
     * @Gedmo\Slug(fields={"username"})
     * @ORM\Column(length=128, nullable=true)
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity="Book", inversedBy="favourites")
     * @ORM\JoinTable(name="users_favourites")
     **/
    private $favourites;

    /**
     * @ORM\OneToMany(targetEntity="BookList", mappedBy="user")
     **/
    private $lists;

    /**
     * @ORM\OneToMany(targetEntity="BookRating", mappedBy="user")
     **/
    private $bookRatings;

    /**
     * @ORM\OneToMany(targetEntity="UserStatus", mappedBy="user")
     **/
    private $statuses;


    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="user")
     **/
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity="ReviewRating", mappedBy="user")
     **/
    private $reviewRatings;

    /**
     * @ORM\OneToMany(targetEntity="BookUserRelation", mappedBy="user")
     **/
    private $bookRelations;

    /**
     * @ORM\ManyToMany(targetEntity="BookList", mappedBy="followers")
     * @ORM\JoinTable(name="lists_follower")
     * @JMS\Expose
     * @JMS\MaxDepth(0)
     **/
    private $listsFollowed;

    /**
     * @ORM\ManyToMany(targetEntity="Book", inversedBy="followers")
     * @ORM\JoinTable(name="book_follower")
     * @JMS\Expose
     * @JMS\MaxDepth(0)
     **/
    private $booksFollowed;

    /**
     * @ORM\ManyToMany(targetEntity="Author", inversedBy="followers")
     * @ORM\JoinTable(name="author_follower")
     * @JMS\Expose
     * @JMS\MaxDepth(0)
     **/
    private $authorsFollowed;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="followers")
     * @JMS\Expose
     * @JMS\MaxDepth(0)
     **/
    private $usersFollowed;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="usersFollowed")
     * @ORM\JoinTable(name="user_follower")
     **/
    private $followers;

    /**
     * @ORM\Column(length=100, nullable=true, name="facebookId")
     **/
    private $facebook_id;

    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;

    /**
     * @ORM\Column(length=500, nullable=true)
     **/
    private $name;

    /**
     * @ORM\Column(length=1, nullable=true)
     **/
    private $gender;

    /**
     * @ORM\Column(length=50, nullable=true)
     **/
    private $country;

    /**
     * @ORM\Column(length=50, nullable=true)
     **/
    private $city;

    /**
     * @ORM\Column(length=500, nullable=true)
     **/
    private $cita;

    /**
     * @ORM\Column(type="date", nullable=true)
     **/
    private $birthday;

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

    /** @ORM\OneToMany(targetEntity="Comment", mappedBy="createdBy") **/
    private $comments;

    /**
    * @ORM\Column(type="boolean", nullable=true)
    */
    private $publicProfile;

    /**
    * @ORM\Column(type="boolean", nullable=true)
    */
    private $globalFollow;

    /**
     * @ORM\Column(length=256, nullable=true)
     */
    private $imageDir;

    /**
     * @ORM\Column(type="integer")
     */
    private $followersCount;

    public function __construct()
    {
        parent::__construct();

        $this->followersCount = 0;
        $this->globalFollow = false;
        $this->favourites = new ArrayCollection();
        $this->lists = new ArrayCollection();
        $this->bookRatings = new ArrayCollection();
        $this->reviewRatings = new ArrayCollection();
        $this->bookRelations = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->authorsFollowed = new ArrayCollection();
        $this->usersFollowed = new ArrayCollection();
        $this->booksFollowed = new ArrayCollection();
        $this->listsFollowed = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->statuses = new ArrayCollection();
        $this->publicProfile = true;
        // your own logic
    }

    /** @ORM\PreUpdate */
    public function setCounts()
    {
        $this->followersCount = $this->followers->count();
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPublicProfile(){
        return $this->publicProfile;
    }

    public function setPublicProfile($publicProfile){
        $this->publicProfile = $publicProfile;
        return $this;
    }

    public function getSlug(){
        return $this->slug;
    }

    public function setSlug($slug){
        $this->slug = $slug;
        return $this;
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
     * @return User
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
     * Set created

     *
     * @param \DateTime $created
     * @return Book
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
     * @return Book
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

    public function getCita(){
        return $this->cita;
    }

    public function setCita($cita){
        $this->cita = $cita;
        return $this;
    }

    /**
     * Set imageDir
     *
     * @param string $imageDir
     * @return Book
     */
    public function setImageDir($imageDir)
    {
        $this->imageDir = $imageDir;

        return $this;
    }

    /**
     * Get imageDir
     *
     * @return string
     */
    public function getImageDir()
    {
        return $this->imageDir;
    }

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        $this->updated= new \DateTime();
    }

    public function getFile(){
        return $this->file;
    }


    /** @ORM\PrePersist */
     public function prePersist() {
        $this->upload();
    }

     /** @ORM\PreUpdate */
    public function preUpdate() {
        $this->upload();
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {

        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        $path=$this->generateRoute();
        $filename = sha1(uniqid(mt_rand(), true));
        $filename = $filename.'.'.$this->getFile()->guessExtension();

        $fs=new Filesystem();
        if(!$fs->exists($path))
        {
                 $fs->mkdir($path,0775);
        }
        $this->getFile()->move($path,$filename);

        $this->imageDir = $this->getUploadDir().$filename;

    }

    public function getAbsolutePath()
    {
        return null === $this->imageDir
            ? null
            : $this->getUploadRootDir().'/'.$this->imageDir;
    }

    public function getWebPath()
    {
        return null === $this->imageDir
            ? null
            : $this->getUploadDir().'/'.$this->imageDir;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../web/wp-content/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return '/uploads/avatars/'.date('mY')."/";
    }

    protected function generateRoute()
    {
        return $this->getUploadRootDir();
    }

    /**
     * Add favourites
     *
     * @param \AppBundle\Entity\Book $favourites
     * @return User
     */
    public function addFavourite(\AppBundle\Entity\Book $favourites)
    {
        $this->favourites[] = $favourites;

        return $this;
    }

    /**
     * Remove favourites
     *
     * @param \AppBundle\Entity\Book $favourites
     */
    public function removeFavourite(\AppBundle\Entity\Book $favourites)
    {
        $this->favourites->removeElement($favourites);
    }

    /**
     * Get favourites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFavourites()
    {
        return $this->favourites;
    }

    /**
     * Add lists
     *
     * @param \AppBundle\Entity\BookList $lists
     * @return User
     */
    public function addList(\AppBundle\Entity\BookList $lists)
    {
        $this->lists[] = $lists;

        return $this;
    }

    /**
     * Remove lists
     *
     * @param \AppBundle\Entity\BookList $lists
     */
    public function removeList(\AppBundle\Entity\BookList $lists)
    {
        $this->lists->removeElement($lists);
    }

    /**
     * Get lists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLists()
    {
        return $this->lists;
    }

    public function getStatuses(){
        return $this->statuses;
    }

    public function addStatus(\AppBundle\Entity\UserStatus $status)
    {
        $this->statuses[] = $status;
        return $this;
    }

    public function removeStatus(\AppBundle\Entity\UserStatus $status)
    {
        $this->statuses->removeElement($status);
    }

    /**
     * Add bookRatings
     *
     * @param \AppBundle\Entity\Rating $bookRatings
     * @return User
     */
    public function addBookRating(\AppBundle\Entity\BookRating $bookRatings)
    {
        $this->bookRatings[] = $bookRatings;

        return $this;
    }

    /**
     * Remove bookRatings
     *
     * @param \AppBundle\Entity\Rating $bookRatings
     */
    public function removeBookRating(\AppBundle\Entity\BookRating $bookRatings)
    {
        $this->bookRatings->removeElement($bookRatings);
    }

    /**
     * Get bookRatings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookRatings()
    {
        return $this->bookRatings;
    }

    /**
     * Add reviewRatings
     *
     * @param \AppBundle\Entity\ReviewRating $reviewRatings
     * @return User
     */
    public function addReviewRating(\AppBundle\Entity\ReviewRating $reviewRatings)
    {
        $this->reviewRatings[] = $reviewRatings;

        return $this;
    }

    /**
     * Remove reviewRatings
     *
     * @param \AppBundle\Entity\ReviewRating $reviewRatings
     */
    public function removeReviewRating(\AppBundle\Entity\ReviewRating $reviewRatings)
    {
        $this->reviewRatings->removeElement($reviewRatings);
    }

    /**
     * Get reviewRatings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviewRatings()
    {
        return $this->reviewRatings;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebook_id)
    {
        $this->facebook_id = $facebook_id;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
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
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Add review
     *
     * @param \AppBundle\Entity\Review $review
     * @return User
     */
    public function addReview(\AppBundle\Entity\Review $review)
    {
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * Remove review
     *
     * @param \AppBundle\Entity\Review $review
     */
    public function removeReview(\AppBundle\Entity\Review $review)
    {
        $this->reviews->removeElement($review);
    }

    /**
     * Get review
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Set facebook_access_token
     *
     * @param string $facebookAccessToken
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebook_access_token
     *
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Add listsFollowed
     *
     * @param \AppBundle\Entity\BookList $listsFollowed
     * @return User
     */
    public function addListsFollowed(\AppBundle\Entity\BookList $listsFollowed)
    {
        $this->listsFollowed[] = $listsFollowed;

        return $this;
    }

    /**
     * Remove listsFollowed
     *
     * @param \AppBundle\Entity\BookList $listsFollowed
     */
    public function removeListsFollowed(\AppBundle\Entity\BookList $listsFollowed)
    {
        $this->listsFollowed->removeElement($listsFollowed);
    }

    /**
     * Get listsFollowed
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListsFollowed()
    {
        return $this->listsFollowed;
    }

    /**
     * Add bookRelations
     *
     * @param \AppBundle\Entity\BookUserRelation $bookRelations
     * @return User
     */
    public function addBookRelation(\AppBundle\Entity\BookUserRelation $bookRelations)
    {
        $this->bookRelations[] = $bookRelations;

        return $this;
    }

    /**
     * Remove bookRelations
     *
     * @param \AppBundle\Entity\BookUserRelation $bookRelations
     */
    public function removeBookRelation(\AppBundle\Entity\BookUserRelation $bookRelations)
    {
        $this->bookRelations->removeElement($bookRelations);
    }

    /**
     * Get bookRelations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookRelations()
    {
        return $this->bookRelations;
    }

    /**
     * Add booksFollowed
     *
     * @param \AppBundle\Entity\Book $booksFollowed
     * @return User
     */
    public function addBooksFollowed(\AppBundle\Entity\Book $booksFollowed)
    {
        $this->booksFollowed[] = $booksFollowed;

        return $this;
    }

    /**
     * Remove booksFollowed
     *
     * @param \AppBundle\Entity\Book $booksFollowed
     */
    public function removeBooksFollowed(\AppBundle\Entity\Book $booksFollowed)
    {
        $this->booksFollowed->removeElement($booksFollowed);
    }

    /**
     * Get booksFollowed
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBooksFollowed()
    {
        return $this->booksFollowed;
    }

    /**
     * Add authorsFollowed
     *
     * @param \AppBundle\Entity\Author $authorsFollowed
     * @return User
     */
    public function addAuthorsFollowed(\AppBundle\Entity\Author $authorsFollowed)
    {
        $this->authorsFollowed[] = $authorsFollowed;

        return $this;
    }

    /**
     * Remove authorsFollowed
     *
     * @param \AppBundle\Entity\Author $authorsFollowed
     */
    public function removeAuthorsFollowed(\AppBundle\Entity\Author $authorsFollowed)
    {
        $this->authorsFollowed->removeElement($authorsFollowed);
    }

    /**
     * Get authorsFollowed
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthorsFollowed()
    {
        return $this->authorsFollowed;
    }

    /**
     * Add usersFollowed
     *
     * @param \AppBundle\Entity\User $usersFollowed
     * @return User
     */
    public function addUsersFollowed(\AppBundle\Entity\User $usersFollowed)
    {
        $this->usersFollowed[] = $usersFollowed;

        return $this;
    }

    /**
     * Remove usersFollowed
     *
     * @param \AppBundle\Entity\User $usersFollowed
     */
    public function removeUsersFollowed(\AppBundle\Entity\User $usersFollowed)
    {
        $this->usersFollowed->removeElement($usersFollowed);
    }

    /**
     * Get usersFollowed
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsersFollowed()
    {
        return $this->usersFollowed;
    }

    /**
     * Add followers
     *
     * @param \AppBundle\Entity\User $followers
     * @return User
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
     * @return User
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
}
