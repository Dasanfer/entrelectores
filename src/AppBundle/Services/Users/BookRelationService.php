<?php
namespace AppBundle\Services\Users;
use AppBundle\Entity\BookUserRelation;
use AppBundle\Services\Timeline\TimelineEvent;
use AppBundle\Entity\FollowEvent;
use AppBundle\Entity\BookList;

class BookRelationService extends \Twig_Extension {
    private $doctrine;
    private $eventDispatcher;

    public function __construct($doctrine,$eventDispatcher){
        $this->doctrine = $doctrine;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getFunctions()
    {
        return array(
            'reading' => new \Twig_Function_Method($this, 'isReading'),
            'readingList' => new \Twig_Function_Method($this, 'readingList'),
            'numReading' => new \Twig_Function_Method($this, 'numReading'),
            'read' => new \Twig_Function_Method($this, 'hasRead'),
            'readList' => new \Twig_Function_Method($this, 'readList'),
            'wants' => new \Twig_Function_Method($this, 'wantsToRead'),
            'wantsList' => new \Twig_Function_Method($this, 'wantsToReadList'),
            'relation' => new \Twig_Function_Method($this, 'getRelation'),
            'publicLists' => new \Twig_Function_Method($this, 'getUserPublicLists'),
            'publicListsCount' => new \Twig_Function_Method($this, 'getUserPublicListsCount')
        );
    }


    public function isReading($user,$book){
        $em = $this->doctrine->getManager();
        $query = $em->createQuery('SELECT count(br) FROM AppBundle\Entity\BookUserRelation br WHERE br.user = :user and br.book = :book and br.beginRead is not null and br.endRead is null');
        $query->setParameters(['user' => $user, 'book' => $book]);
        $count = $query->getSingleScalarResult();
        return $count > 0;
    }

    public function hasRead($user,$book){
        $em = $this->doctrine->getManager();
        $query = $em->createQuery('SELECT count(br) FROM AppBundle\Entity\BookUserRelation br WHERE br.user = :user and br.book = :book and br.endRead is not null');
        $query->setParameters(['user' => $user, 'book' => $book]);
        $count = $query->getSingleScalarResult();
        return $count > 0;
    }

    public function readList($user){
        return $this->readBooksQuery($user)->getResult();
    }

    public function readingList($user){
        return $this->readingBooksQuery($user)->getResult();
    }

    public function wantsToReadList($user){
        return $this->wantsBooksQuery($user)->getResult();
    }

    public function wantsToRead($user,$book){
        $em = $this->doctrine->getManager();
        $query = $em->createQuery('SELECT count(br) FROM AppBundle\Entity\BookUserRelation br WHERE br.user = :user and br.book = :book and br.beginRead is null and br.endRead is null and br.want is not null');
        $query->setParameters(['user' => $user, 'book' => $book]);
        $count = $query->getSingleScalarResult();
        return $count > 0;
    }

    public function numReading($book){
        $em = $this->doctrine->getManager();
        $query = $em->createQuery('SELECT count(br) FROM AppBundle\Entity\BookUserRelation br WHERE br.book = :book and br.beginRead is not null and br.endRead is null');
        $query->setParameters(['book' => $book]);
        $count = $query->getSingleScalarResult();
        return $count;
    }

    public function readBooksQuery($user){
        $em = $this->doctrine->getManager();
        return $em->createQuery('SELECT b FROM AppBundle\Entity\Book b join b.userRelations br WHERE br.user = :user and br.endRead is not null')->setParameters(array('user' => $user));
    }

    public function readingBooksQuery($user){
        $em = $this->doctrine->getManager();
        return $em->createQuery('SELECT b FROM AppBundle\Entity\Book b join b.userRelations br WHERE br.user = :user and br.beginRead is not null and br.endRead is null')->setParameters(array('user' => $user));
    }

    public function wantsBooksQuery($user){
        $em = $this->doctrine->getManager();
        return $em->createQuery('SELECT b FROM AppBundle\Entity\Book b join b.userRelations br WHERE br.user = :user and br.want is not null and br.beginRead is null and br.endRead is null')->setParameters(array('user' => $user));
    }

    public function getUserPublicLists($user){
        $em = $this->doctrine->getManager();
        $query = $em->createQuery('SELECT l FROM AppBundle\Entity\BookList l WHERE l.user = :user and l.publicFlag >= :publicFlag');
        $query->setParameters(array('user' => $user,'publicFlag' => BookList::READ_PUBLIC));
        $lists = $query->getResult();
        return $lists;
    }

    public function getUserPublicListsCount($user){
        $em = $this->doctrine->getManager();
        $query = $em->createQuery('SELECT count(l) FROM AppBundle\Entity\BookList l WHERE l.user = :user and l.publicFlag >= :publicFlag');
        $query->setParameters(array('user' => $user,'publicFlag' => BookList::READ_PUBLIC));
        $lists = $query->getSingleScalarResult();

        return $lists;
    }

    public function getRelation($user, $book){
        $repo = $this->doctrine->getRepository('AppBundle:BookUserRelation');
        return $repo->findOneBy(array('user' => $user, 'book' => $book));
    }

    public function addReading($user, $book){
        $relation = $this->setupRelation($user,$book);
        $relation->setBeginRead(new \DateTime());
        $relation->setEndRead(null);
        $this->doctrine->getManager()->persist($relation);
    }

    public function addRead($user, $book){
        $relation = $this->setupRelation($user,$book);

        if($relation->getEndRead() == null){
            $relation->setEndRead(new \DateTime());
            $this->doctrine->getManager()->persist($relation);
        }
    }

    public function addWant($user, $book){
        $relation = $this->setupRelation($user,$book);
        $relation->setWant(new \DateTime());
        $relation->setEndRead(null);
        $relation->setBeginRead(null);
        $this->doctrine->getManager()->persist($relation);
    }

    private function setupRelation($user, $book){
        $relation = $this->getRelation($user, $book);
        if(is_null($relation)){
            $relation = new BookUserRelation();
            $relation->setBook($book);
            $relation->setUser($user);
        }
        return $relation;
    }

    public function deleteRelation($user,$book){
        $relation = $this->getRelation($user, $book);
        if(!is_null($relation)){
            $this->doctrine->getManager()->remove($relation);
        }
    }

    public function getName()
    {
        return 'app_book_relation';
    }
}
