<?php
namespace AppBundle\Services\Timeline;

use Symfony\Component\EventDispatcher\Event;
use AppBundle\Entity\User;
use AppBundle\Entity\Book;
use AppBundle\Entity\Author;
use AppBundle\Entity\Booklist;

class TimelineEvent extends Event
{
    protected $createdBy;
    protected $user;
    protected $status;
    protected $book;
    protected $author;
    protected $list;
    protected $type;
    protected $review;

    public function __construct(User $createdBy,$type = null,Book $book = null,BookList $list  = null,Author $author  = null,User $user = null,$comment = null)
    {
        $this->createdBy = $createdBy;
        $this->type = $type;
        $this->user = $user;
        $this->book = $book;
        $this->list = $list;
        $this->comment = $comment;
    }

    public function setStatus($status){
        $this->status = $status;
    }

    public function getStatus(){
        return $this->status;
    }

    public function setComment($comment){
        $this->comment = $comment;
        return $this;
    }

    public function getComment(){
        return $this->comment;
    }

    public function setUser($user){
        $this->user = $user;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setCreatedBy($createdBy){
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setBook($book){
        $this->book = $book;
        return $this;
    }

    public function getBook()
    {
        return $this->book;
    }

    public function setList($list){
        $this->list = $list;
        return $this;
    }

    public function getList()
    {
        return $this->list;
    }

    public function setAuthor($author){
        $this->author = $author;
        return $this;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setType($type){
        $this->type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setReview($review){
        $this->review = $review;
        return $this;
    }

    public function getReview(){
        return $this->review;
    }
}
