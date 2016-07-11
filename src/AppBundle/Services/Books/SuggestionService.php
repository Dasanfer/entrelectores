<?php
namespace AppBundle\Services\Books;

class SuggestionService
{
    private $doctrine;

    public function __construct($doctrine){
        $this->doctrine = $doctrine;
    }

    public function getSuggestionsFromBook($book, $number = 3, $minRate = 8){
        $em = $this->doctrine->getManager();
        $query = $em->createQuery('SELECT u FROM AppBundle\Entity\User u join u.bookRatings r join r.book b WHERE r.value >= :value and b.id = :book order by r.created DESC');
        $query->setParameters(array('book' => $book->getId(), 'value' => $minRate));
        $users = $query->setMaxResults(20)->getResult();

        $userIds = array();
        foreach($users as $user){
            $userIds[] = $user->getId();
        }

        $query = $em->createQuery('SELECT b FROM AppBundle\Entity\Book b join b.ratings r join r.user u WHERE r.value >= :value and u.id in (:userIds) and b.id != :bookId order by r.value DESC');
        $query->setParameters(array('userIds' => $userIds, 'value' => $minRate, 'bookId' => $book->getId()))->setMaxResults($number);
        $books = $query->getResult();

        if(count($books) < $number && $minRate > 6)
            return $this->getSuggestionsFromBook($book,$number,$minRate-1);

        return $books;
    }

    public function getSuggestionsFromBookAndUser($book, $user, $number = 3, $minRate = 8){
        $em = $this->doctrine->getManager();
        $query = $em->createQuery('SELECT u FROM AppBundle\Entity\User u join u.bookRatings r join r.book b WHERE r.value >= :value and b.id = :book order by r.value DESC');
        $query->setParameters(array('book' => $book->getId(), 'value' => $minRate));
        $users = $query->setMaxResults(10)->getResult();

        $userIds = array();
        foreach($users as $refUser){
            $userIds[] = $refUser->getId();
        }

        $query = $em->createQuery('SELECT b FROM AppBundle\Entity\Book b join b.ratings r join r.user u WHERE r.value >= :value and u.id in (:userIds) and b.id not in (:bookIds) and b.genre = :genre order by r.value DESC');

        $bookIds = array($book->getId());
        foreach($user->getBookRatings() as $rating){
            $bookIds[] = $rating->getBook()->getId();
        }

        $query->setParameters(
            array(
                'userIds' => $userIds,
                'value' => $minRate,
                'bookIds' => $bookIds,
                'genre' => $book->getGenre()
            )
        )->setMaxResults($number);
        $books = $query->getResult();

        while(count($books) < $number && $minRate > 6){
            $minRate = $minRate - 1;
            $query->setParameters(
                array(
                    'userIds' => $userIds,
                    'value' => $minRate,
                    'bookIds' => $bookIds,
                    'genre' => $book->getGenre()
                )
            )->setMaxResults($number);
            $books = $query->getResult();
        }

        return $books;
    }


    public function getSuggestionsFromUser($user, $number = 3, $minRate = 8){
        $em = $this->doctrine->getManager();

        //get high ratings books from user
        $query = $em->createQuery('SELECT b FROM AppBundle\Entity\Book b join b.ratings r join r.user u WHERE r.value >= :value and u.id = :user order by r.created DESC');
        $query->setParameters(array('user' => $user->getId(), 'value' => $minRate));
        $highUserRating = $query->setMaxResults(30)->getResult();

        $userBookIds = array();
        foreach($highUserRating as $rating){
            $userBookIds[] = $rating->getId();
        }

        //get high ratings users of same books
        $query = $em->createQuery('SELECT u FROM AppBundle\Entity\User u join u.bookRatings r join r.book b WHERE r.value >= :value and b.id in (:books) order by r.value DESC');
        $query->setParameters(array('books' => $userBookIds, 'value' => $minRate));
        $relatedUsers = $query->setMaxResults(30)->getResult();

        $userIds = array();
        foreach($relatedUsers as $relatedUser){
            $userIds[] = $relatedUser->getId();
        }

        //get high ratings books of same users
        $query = $em->createQuery('SELECT b FROM AppBundle\Entity\Book b join b.ratings r join r.user u WHERE r.value >= :value and u.id in (:userIds) and b.id not in (:bookIds) order by r.value DESC');
        $query->setParameters(array('userIds' => $userIds, 'value' => $minRate, 'bookIds' => $userBookIds))->setMaxResults($number);

        $books = $query->getResult();

        if(count($books) < $number && $minRate > 6)
            return $this->getSuggestionsFromUser($user,$number,$minRate-1);

        return $books;
    }
}
