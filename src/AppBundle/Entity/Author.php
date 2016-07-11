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
class Author
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
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $searchString;

    /**
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $oldSlug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $info;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $popular;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $richInfo;

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
     * @ORM\OneToMany(targetEntity="Book", mappedBy="author")
     **/
    private $books;

    /**
     * @ORM\Column(length=256, nullable=true)
     */
    private $imageDir;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="authorsFollowed")
     **/
    private $followers;

    /**
     * @ORM\Column(type="integer")
     */
    private $followersCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $bookCount;

    /** @ORM\OneToMany(targetEntity="Comment", mappedBy="author") **/
    private $comments;

    public function __construct()
    {
        $this->followersCount = 0;
        $this->bookCount = 0;
        $this->books = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->comments = new ArrayCollection();
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


    public function searchType(){
        return 'author';
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
     * Set name
     *
     * @param string $name
     * @return Author
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
     * Set info
     *
     * @param string $info
     * @return Author
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Add books
     *
     * @param \AppBundle\Entity\Book $books
     * @return Author
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

    public function __toString() {
        return $this->name;
    }

    /**
     * Set imageDir
     *
     * @param string $imageDir
     * @return Author
     */
    public function setImageDir($imageDir)
    {
        if(is_null($this->imageDir))
            return 'no-cover.jpg';

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
     * Set richInfo
     *
     * @param string $richInfo
     * @return Author
     */
    public function setRichInfo($richInfo)
    {
        $this->richInfo = $richInfo;

        return $this;
    }

    /**
     * Get richInfo
     *
     * @return string
     */
    public function getRichInfo()
    {
        return $this->richInfo;
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
        return 'uploads/autores/'.date('mY')."/";
    }

    protected function generateRoute()
    {

        return $this->getUploadRootDir();
    }

    /**
     * Add followers
     *
     * @param \AppBundle\Entity\User $followers
     * @return Author
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
     * @return Author
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
     * @return Author
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
     * Set followersCount
     *
     * @param integer $followersCount
     * @return Author
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
     * @return Author
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

    /**
     * Set searchString
     *
     * @param string $searchString
     * @return Author
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
