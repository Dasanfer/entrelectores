<?php
namespace AppBundle\Services\Books;

class ReviewService
{
    private $doctrine;

    public function __construct($doctrine){
        $this->doctrine = $doctrine;
    }

    public function reviewExists($user,$book){
        $em = $this->doctrine->getManager();
        $query = $em->createQuery('SELECT count(r) FROM AppBundle\Entity\Review r join r.book b join r.user u WHERE u.id = :userId and b.id = :bookId');
        $query->setParameters(array('bookId' => $book->getId(), 'userId' => $user->getId()));

        $count = $query->getSingleScalarResult();
        return $count > 0;
    }

    public function bookReviews($book,$offset = 0, $limit = 10){
        $em = $this->doctrine->getManager();
        $query = $em->createQuery('SELECT r,u FROM AppBundle\Entity\Review r join r.book b join r.user u WHERE b.id = :bookId order by r.created DESC');
        $query->setParameters(array('bookId' => $book->getId()));
        $query->setMaxResults($limit);
        $query->setFirstResult($offset);
        return $query->getResult();
    }
}
