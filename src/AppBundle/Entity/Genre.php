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

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Genre
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
     * @ORM\Column(type="string", length=256)
     */
    protected $info;

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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $featured;

    /**
     * @ORM\OneToMany(targetEntity="Book", mappedBy="genre")
     **/
    private $books;

    /**
     * @ORM\Column(type="string", length=512)
     */
    protected $description;

    /**
     * @ORM\Column(length=256, nullable=true)
     */
    private $imageDir;

    public function __construct()
    {
        $this->books = new ArrayCollection();
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
     * Set featured
     *
     * @param boolean $check
     * @return Genre
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

    public function __toString() {
        return $this->name;
    }


    /**
     * Set info
     *
     * @param string $info
     * @return Genre
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

    /**
     * Set description
     *
     * @param string $description
     * @return Genre
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set imageDir
     *
     * @param string $imageDir
     * @return Book
     */
    public function setImageDir($imageDir)
    {
        die("setImageDir");
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


    /**
    @ORM\PrePersist
    */
     public function prePersist() {
        $this->upload();
    }

     /**
     @ORM\PreUpdate
     */
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
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/generos/'.date('mY')."/";
    }

    protected function generateRoute()
    {

        return $this->getUploadRootDir();
    }


}
