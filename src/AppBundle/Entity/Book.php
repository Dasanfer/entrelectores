<?php
// src/Acme/UserBundle/Entity/User.php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use JMS\Serializer\Annotation as JMS;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class Book
{
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
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $searchString;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $originalTitle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $year;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $isbn;

    /**
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(length=256, nullable=true)
     */
    private $oldSlug;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $promoted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $popular;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $novelty;

    /**
     * @ORM\Column(length=256, nullable=true)
     */
    private $imageDir;

    /**
     * @ORM\Column(type="string", length=4096, nullable=true)
     */
    protected $sinopsis;

    /**
     * @ORM\OneToMany(targetEntity="BookUserRelation", mappedBy="book")
     **/
    private $userRelations;

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
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="books")
     **/
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Genre", inversedBy="books")
     **/
    private $genre;

    /** @ORM\ManyToMany(targetEntity="User", mappedBy="favourites") **/
    private $favourites;

    /** @ORM\ManyToMany(targetEntity="BookList", mappedBy="books") **/
    private $lists;

    /** @ORM\OneToMany(targetEntity="Review", mappedBy="book") **/
    private $reviews;

    /** @ORM\OneToMany(targetEntity="BookRating", mappedBy="book") **/
    private $ratings;

    /** @ORM\Column(type="float") **/
    private $cachedRate;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="booksFollowed")
     **/
    private $followers;

    /** @ORM\OneToMany(targetEntity="Comment", mappedBy="book") **/
    private $comments;

    private $authorAux;

    /**
     * @ORM\Column(type="integer")
     */
    private $followersCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $ratingCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $reviewCount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $elcId;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $featured;

    public function __construct()
    {
        $this->followersCount = 0;
        $this->ratingCount = 0;
        $this->reviewCount = 0;

        $this->favourites = new ArrayCollection();
        $this->lists = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->cachedRate = 5;
    }

    public function searchType(){
        return 'book';
    }

    /** @ORM\PreUpdate */
    public function setCounts()
    {
        $this->followersCount = $this->followers->count();
        $this->ratingCount = $this->ratings->count();
        $this->reviewCount = $this->reviews->count();
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
     * Set title
     *
     * @param string $title
     * @return Book
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
     * Set slug
     *
     * @param string $slug
     * @return Book
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
     * Set sinopsis
     *
     * @param string $sinopsis
     * @return Book
     */
    public function setSinopsis($sinopsis)
    {
        $this->sinopsis = $sinopsis;

        return $this;
    }

    /**
     * Get sinopsis
     *
     * @return string
     */
    public function getSinopsis()
    {
        return $this->sinopsis;
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

    /**
     * Set oldSlug
     *
     * @param string $oldSlug
     * @return Book
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
     * Set author
     *
     * @param \AppBundle\Entity\Author $author
     * @return Book
     */
    public function setAuthorAux($author)
    {
        $this->authorAux = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\Author
     */
    public function getAuthorAux()
    {
        return $this->authorAux;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\Author $author
     * @return Book
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
     * Add favourites
     *
     * @param \AppBundle\Entity\User $favourites
     * @return Book
     */
    public function addFavourite(\AppBundle\Entity\User $favourites)
    {
        $this->favourites[] = $favourites;

        return $this;
    }

    /**
     * Remove favourites
     *
     * @param \AppBundle\Entity\User $favourites
     */
    public function removeFavourite(\AppBundle\Entity\User $favourites)
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
     * @return Book
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

    /**
     * Add reviews
     *
     * @param \AppBundle\Entity\Review $reviews
     * @return Book
     */
    public function addReview(\AppBundle\Entity\Review $reviews)
    {
        $this->reviews[] = $reviews;

        return $this;
    }

    /**
     * Remove reviews
     *
     * @param \AppBundle\Entity\Review $reviews
     */
    public function removeReview(\AppBundle\Entity\Review $reviews)
    {
        $this->reviews->removeElement($reviews);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Add ratings
     *
     * @param \AppBundle\Entity\BookRating $ratings
     * @return Book
     */
    public function addRating(\AppBundle\Entity\BookRating $rating)
    {
        $this->ratings[] = $rating;

        return $this;
    }

    /**
     * Remove ratings
     *
     * @param \AppBundle\Entity\BookRating $ratings
     */
    public function removeRating(\AppBundle\Entity\BookRating $rating)
    {
        $this->ratings->removeElement($rating);
    }

    /**
     * Get ratings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * Set isbn
     *
     * @param string $isbn
     * @return Book
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * Get isbn
     *
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    public function getIsbn10() {
        $isbn = $this->isbn;
        if (preg_match('/^\d{3}(\d{9})\d$/', $isbn, $m)) {
            $sequence = $m[1];
            $sum = 0;
            $mul = 10;
            for ($i = 0; $i < 9; $i++) {
                $sum = $sum + ($mul * (int) $sequence{$i});
                $mul--;
            }
            $mod = 11 - ($sum%11);
            if ($mod == 10) {
                $mod = "X";
            }
            else if ($mod == 11) {
                $mod = 0;
            }
            $isbn = $sequence.$mod;
        }
        return $isbn;
    }

    public function isValidIsbn(){
        $str = $this->getIsbn10();
        $regex = '/\b(?:ISBN(?:: ?| ))?((?:97[89])?\d{9}[\dx])\b/i';
        return preg_match($regex, str_replace('-', '', $str), $matches);
    }

    /**
     * Set genre
     *
     * @param \AppBundle\Entity\Genre $genre
     * @return Book
     */
    public function setGenre(\AppBundle\Entity\Genre $genre = null)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return \AppBundle\Entity\Genre
     */
    public function getGenre()
    {
        return $this->genre;
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
        if(is_null($this->imageDir) || !$this->imageDir)
            return '/no-cover.jpg';

        return $this->imageDir;
    }

    /**
     * Set originalTitle
     *
     * @param string $originalTitle
     * @return Book
     */
    public function setOriginalTitle($originalTitle)
    {
        $this->originalTitle = $originalTitle;

        return $this;
    }

    /**
     * Get originalTitle
     *
     * @return string
     */
    public function getOriginalTitle()
    {
        return $this->originalTitle;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Book
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    public function __toString() {
        return $this->title;
    }


    /**
     * Set cachedRate
     *
     * @param float $cachedRate
     * @return Book
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
        if($this->cachedRate < 1)
            return 5;
        else
            return $this->cachedRate;
    }

    /**
     * Set promoted
     *
     * @param boolean $promoted
     * @return Book
     */
    public function setPromoted($promoted)
    {
        $this->promoted = $promoted;

        return $this;
    }

    /**
     * Get promoted
     *
     * @return boolean
     */
    public function getPromoted()
    {
        return $this->promoted;
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

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
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
        return '/uploads/portadas/'.date('mY')."/";
    }

    protected function generateRoute()
    {

        return $this->getUploadRootDir();
    }


    /**
     * Add userRelations
     *
     * @param \AppBundle\Entity\BookUserRelation $userRelations
     * @return Book
     */
    public function addUserRelation(\AppBundle\Entity\BookUserRelation $userRelations)
    {
        $this->userRelations[] = $userRelations;

        return $this;
    }

    /**
     * Remove userRelations
     *
     * @param \AppBundle\Entity\BookUserRelation $userRelations
     */
    public function removeUserRelation(\AppBundle\Entity\BookUserRelation $userRelations)
    {
        $this->userRelations->removeElement($userRelations);
    }

    /**
     * Get userRelations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserRelations()
    {
        return $this->userRelations;
    }

    /**
     * Add followers
     *
     * @param \AppBundle\Entity\User $followers
     * @return Book
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
     * @return Book
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
     * Set popular
     *
     * @param boolean $popular
     * @return Book
     */
    public function setPopular($popular)
    {
        $this->popular = $popular;

        return $this;
    }

    /**
     * Get popular
     *
     * @return boolean
     */
    public function getPopular()
    {
        return $this->popular;
    }

    /**
     * Set novelty
     *
     * @param boolean $novelty
     * @return Book
     */
    public function setNovelty($novelty)
    {
        $this->novelty = $novelty;

        return $this;
    }

    /**
     * Get novelty
     *
     * @return boolean
     */
    public function getNovelty()
    {
        return $this->novelty;
    }

    /**
     * Set elcId
     *
     * @param string $elcId
     * @return Book
     */
    public function setElcId($elcId)
    {
        $this->elcId = $elcId;

        return $this;
    }

    /**
     * Get elcId
     *
     * @return string
     */
    public function getElcId()
    {
        return $this->elcId;
    }

    /**
     * Set ratingCount
     *
     * @param integer $ratingCount
     * @return Book
     */
    public function setRatingCount($ratingCount)
    {
        $this->ratingCount = $ratingCount;

        return $this;
    }

    /**
     * Get ratingCount
     *
     * @return integer
     */
    public function getRatingCount()
    {
        return $this->ratingCount;
    }

    /**
     * Set reviewCount
     *
     * @param integer $reviewCount
     * @return Book
     */
    public function setReviewCount($reviewCount)
    {
        $this->reviewCount = $reviewCount;

        return $this;
    }

    /**
     * Get reviewCount
     *
     * @return integer
     */
    public function getReviewCount()
    {
        return $this->reviewCount;
    }

    /**
     * Set featured
     *
     * @param boolean $check
     * @return Book
     */
    public function setFeatured($check)
    {
        $this->featured = $check;

        return $this;
    }

    /**
     * Get featured
     *
     * @return boolean
     */
    public function getFeatured()
    {
        return $this->featured;
    }

    /**
     * Set searchString
     *
     * @param string $searchString
     * @return Book
     */
    public function setSearchString($searchString)
    {
        $this->searchString = $searchString;

        return $this;
    }

    /**
     * Get searchString
     *
     * @return string
     */
    public function getSearchString()
    {
        return $this->searchString;
    }
}
