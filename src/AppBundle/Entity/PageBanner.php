<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * PageBanner
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class PageBanner
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="imgBigDir", type="string", length=255)
     */
    private $imgBigDir;

    /**
     * @var string
     *
     * @ORM\Column(name="imgSmallDir", type="string", length=255)
     */
    private $imgSmallDir;

    /**
     * @var string
     *
     * @ORM\Column(name="targetUrl", type="string", length=255)
     */
    private $targetUrl;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    /**
     * Unmapped property to handle file uploads
     */
    private $file2;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return PageBanner
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
     * Set imgBigDir
     *
     * @param string $imgBigDir
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
     * @return PageBanner
     */
    public function setImgBigDir($imgBigDir)
    {
        $this->imgBigDir = $imgBigDir;

        return $this;
    }

    /**
     * Get imgBigDir
     *
     * @return string
     */
    public function getImgBigDir()
    {
        return $this->imgBigDir;
    }

    /**
     * Set imgSmallDir
     *
     * @param string $imgSmallDir
     * @return PageBanner
     */
    public function setImgSmallDir($imgSmallDir)
    {
        $this->imgSmallDir = $imgSmallDir;

        return $this;
    }

    /**
     * Get imgSmallDir
     *
     * @return string
     */
    public function getImgSmallDir()
    {
        return $this->imgSmallDir;
    }

    /**
     * Set targetUrl
     *
     * @param string $targetUrl
     * @return PageBanner
     */
    public function setTargetUrl($targetUrl)
    {
        $this->targetUrl = $targetUrl;

        return $this;
    }

    /**
     * Get targetUrl
     *
     * @return string
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return PageBanner
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /** @ORM\PrePersist */
    public function prePersist() {
        $this->upload();
    }

    /** @ORM\PreUpdate */
    public function preUpdate() {
        $this->upload();
    }

    public function getFile(){
        return $this->file;
    }

    public function setFile($file){
        $this->updated = new \DateTime();
        $this->file = $file;
        return $this;
    }

    public function getFile2(){
        return $this->file2;
    }

    public function setFile2($file2){
        $this->updated = new \DateTime();
        $this->file2 = $file2;
        return $this;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {

        if (null !== $this->getFile()) {
            $path=$this->generateRoute();
            $filename = sha1(uniqid(mt_rand(), true));
            $filename = $filename.'.'.$this->getFile()->guessExtension();

            $fs=new Filesystem();
            if(!$fs->exists($path))
            {
                $fs->mkdir($path,0775);
            }
            $this->getFile()->move($path,$filename);

            $this->imgBigDir = $this->getUploadDir().$filename;
        }

        if (null !== $this->getFile2()) {
            $path=$this->generateRoute();
            $filename = sha1(uniqid(mt_rand(), true));
            $filename = $filename.'.'.$this->getFile2()->guessExtension();
            $fs=new Filesystem();

            if(!$fs->exists($path))
            {
                $fs->mkdir($path,0775);
            }

            $this->getFile2()->move($path,$filename);

            $this->imgSmallDir = $this->getUploadDir().$filename;
        }
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return '/uploads/banners/'.date('mY')."/";
    }

    protected function generateRoute()
    {

        return $this->getUploadRootDir();
    }



    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return PageBanner
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
     *
     * @return PageBanner
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
}
