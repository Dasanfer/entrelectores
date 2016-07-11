<?php
// src/Acme/UserBundle/Entity/User.php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Poll
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=256)
     */
    protected $title;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    protected $question;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $answer;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $trivia;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $active;

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

    /** @ORM\OneToMany(targetEntity="PollOption", mappedBy="poll",cascade={"persist", "remove"}, orphanRemoval=true) **/
    private $options;

    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->trivia = false;
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
     * Set title
     *
     * @param string $title
     * @return Poll
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
     * @return Poll
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
     * Set question
     *
     * @param string $question
     * @return Poll
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Poll
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
     * @return Poll
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
     * Add options
     *
     * @param \AppBundle\Entity\PollOption $options
     * @return Poll
     */
    public function addOption(\AppBundle\Entity\PollOption $options)
    {
        $options->setPoll($this);
        $this->options[] = $options;

        return $this;
    }

    /**
     * Remove options
     *
     * @param \AppBundle\Entity\PollOption $options
     */
    public function removeOption(\AppBundle\Entity\PollOption $options)
    {
        $this->options->removeElement($options);
    }

    /**
     * Get options
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options) {
        if (count($options) > 0) {
            foreach ($options as $i) {
                $this->addOption($i);
            }
        }

        return $this;
    }


    public function totalAnswers(){
        $sum = 0;
        $options = $this->getOptions();
        foreach($options as $option){
            $sum += $option->getAnswers()->count();
        }

        if($sum == 0)
            $sum = 1;

        return $sum;
    }



    /**
     * Set answer
     *
     * @param string $answer
     * @return Poll
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set trivia
     *
     * @param \bool $trivia
     * @return Poll
     */
    public function setTrivia($trivia)
    {
        $this->trivia = $trivia;

        return $this;
    }

    /**
     * Get trivia
     *
     * @return \bool
     */
    public function getTrivia()
    {
        return $this->trivia;
    }

    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    public function getActive()
    {
        return $this->active;
    }
}
