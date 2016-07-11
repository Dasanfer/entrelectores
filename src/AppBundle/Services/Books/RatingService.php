<?php
namespace AppBundle\Services\Books;
use AppBundle\Entity\BookRating;
use AppBundle\Entity\ReviewRating;
use AppBundle\Services\Timeline\TimelineEvent;
use AppBundle\Entity\FollowEvent;
use Doctrine\ORM\Query\ResultSetMapping;


class RatingService extends \Twig_Extension

{
    private $easing;
    private $doctrine;
    private $eventDispatcher;

    public function __construct($doctrine,$eventDispatcher,$easing = 1){
        $this->easing = $easing;
        $this->doctrine = $doctrine;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getFunctions()
    {
        return array(
            'bookStats' => new \Twig_Function_Method($this, 'bookStats'),
            'showRate' => new \Twig_Function_Method($this, 'showRate'),
            'hasRated' => new \Twig_Function_Method($this, 'hasRated'),
            'showReviewRate' => new \Twig_Function_Method($this, 'showReviewRate'),
            'getReviewRatings' => new \Twig_Function_Method($this, 'getReviewRatings'),
            'getDataCounts' => new \Twig_Function_Method($this, 'getDataCounts'),
        );
    }

    public function newBookRating($user,$book,$value){
        $repo = $this->doctrine->getRepository('AppBundle:BookRating');
        $rating = $repo->findOneBy(array('book' => $book->getId(), 'user' => $user->getId()));

        if(is_null($rating)){
            $rating = new BookRating();
            $rating->setUser($user);
            $rating->setBook($book);
            $book->addRating($rating);
            $user->addBookRating($rating);
            $event = new TimelineEvent($user,FollowEvent::rating,$book);
            $this->eventDispatcher->dispatch('app.timeline_event',$event);
        }

        $rating->setValue($value);
        $this->updateRatingCache($book,$value,$user);
        $this->doctrine->getManager()->persist($rating);
        $this->doctrine->getManager()->persist($book);
        $this->doctrine->getManager()->persist($user);

        return $rating;
    }

    public function newReviewRating($review,$user,$value){
        $repo = $this->doctrine->getRepository('AppBundle:ReviewRating');
        $rating = $repo->findOneBy(array('review' => $review->getId(), 'user' => $user->getId()));

        if(is_null($rating)){
            $rating = new ReviewRating();
            $rating->setUser($user);
            $rating->setReview($review);
            $review->addRating($rating);
            $user->addReviewRating($rating);
        }

        $rating->setValue($value);

        $this->updateReviewRatingCache($review,$value,$user);

        $this->doctrine->getManager()->persist($rating);
        $this->doctrine->getManager()->persist($review);
        $this->doctrine->getManager()->persist($user);

        return $rating;
    }

    public function showRate($book,$user = null){
        if(is_null($user))
            return $book->getCachedRate();

        $repo = $this->doctrine->getRepository('AppBundle:BookRating');
        $rating = $repo->findOneBy(array('book' => $book->getId(), 'user' => $user->getId()));
        if(is_null($rating)){
            return $book->getCachedRate();
        } else {
            return $rating->getValue();
        }
    }

    public function hasRated($book,$user = null){
        if(is_null($user))
            return false;

        $repo = $this->doctrine->getRepository('AppBundle:BookRating');
        $rating = $repo->findOneBy(array('book' => $book->getId(), 'user' => $user->getId()));

        return !is_null($rating);
    }

    public function showReviewRate($review,$user = null){
        if(is_null($user))
            return $book->getCachedRate();

        $repo = $this->doctrine->getRepository('AppBundle:ReviewRating');
        $rating = $repo->findOneBy(array('review' => $review->getId(), 'user' => $user->getId()));
        if(is_null($rating)){
            return 0;
        } else {
            return $rating->getValue();
        }
    }

    public function getReviewRatings($review){
        $em = $this->doctrine->getManager();
        $query = $em->createQuery('SELECT count(r) FROM AppBundle\Entity\ReviewRating r join r.review rev WHERE rev.id = :reviewId and r.value > 0');
        $query->setParameter('reviewId',$review->getId());

        $positive = $query->getSingleScalarResult();
        $query = $em->createQuery('SELECT count(r) FROM AppBundle\Entity\ReviewRating r join r.review rev WHERE rev.id = :reviewId and r.value < 0');
        $query->setParameter('reviewId',$review->getId());

        $negative = $query->getSingleScalarResult();
        return array('positive' => $positive, 'negative' => $negative);
    }

    public function bookStats($book){
        $results = array();
        $em = $this->doctrine->getManager();
        $query = $em->createQuery('SELECT count(r) FROM AppBundle\Entity\BookRating r join r.book b WHERE b.id = :bookId and r.value <= :maxvalue and r.value >= :minvalue');
        for($i = 10; $i > 0; $i = $i - 2){
            $query->setParameters(array(
                'bookId'=> $book->getId(),
                'minvalue' => $i-1,
                'maxvalue' => $i
            ));
            $result = $query->getSingleScalarResult();
            $total = $book->getRatings()->count();

            if($total == 0)
                $total = 1;

            $results[] = array(
                'value' => $i/2,
                'count' => $result,
                'percent' => $result*100/$total
            );
        }

        return $results;
    }

    private function updateRatingCache($book,$value,$user){
        $em = $this->doctrine->getManager();

        $query = $em->createQuery('SELECT sum(r.value) as total, count(r.id) as num FROM AppBundle\Entity\BookRating r join r.book b join r.user u WHERE b.id = :bookId and u.id != :userId');

        $query->setParameters(array(
            'bookId'=> $book->getId(),
            'userId'=> $user->getId(),
        ));

        $result = $query->getResult();
        $total = $result[0]['total'] + $value;
        $num = $result[0]['num'] + 1;
        if($num > 0){
            $rate = $total/$num;
            if($num < 10)
                $rate = $rate * 0.8;

            if($rate < 1)
                $rate = 1;

            $book->setCachedRate($rate);
        }
        else
            $book->setCachedRate(1);

    }

    public function bookRatingUpdate($book){
        $em = $this->doctrine->getManager();
        $query = $em->createQuery('SELECT sum(r.value) as total, count(r.id) as num FROM AppBundle\Entity\BookRating r join r.book b WHERE b.id = :bookId');

        $query->setParameters(array(
            'bookId'=> $book->getId(),
        ));

        $result = $query->getResult();
        $total = $result[0]['total'];
        $num = $result[0]['num'];

        if($num > 0){
            $rate = $total/$num;
            if($num < 10)
                $rate = $rate * 0.8;

            if($rate < 1)
                $rate = 1;

            $book->setCachedRate($rate);
        }
    }

    private function updateReviewRatingCache($review,$value,$user){
        $em = $this->doctrine->getManager();

        $query = $em->createQuery('SELECT sum(r.value) as total, count(r.id) as num FROM AppBundle\Entity\ReviewRating r join r.review re join r.user u WHERE re.id = :reviewId and u.id != :userId');

        $query->setParameters(array(
            'reviewId'=> $review->getId(),
            'userId'=> $user->getId(),
        ));

        $result = $query->getResult();
        $total = $result[0]['total'] + $value;
        $num = $result[0]['num'] + 1;

        if($num > 0){
            $rate = $total/$num;
            $review->setCachedRate($rate);
        }
        else
            $review->setCachedRate(0);

    }

    public function mostVoted($begin,$end,$count){
        $em = $this->doctrine->getManager();

        $query = $em->createQuery('SELECT b, count(r) as hidden score FROM AppBundle\Entity\Book b join b.ratings r where r.value > 7 and r.created > :begin and r.created < :end group by r.book order by score DESC');

        $query->setParameters(array(
            'begin'=> $begin,
            'end'=> $end
        ));

        $result = $query->setMaxResults($count)->getResult();
        return $result;
    }

    public function getDataCounts($book){
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('count(r)')->from('AppBundle:Review','r')
            ->where($qb->expr()->eq('r.book',':book'));
        $qb->setParameter('book',$book);
        $reviewCount = $qb->getQuery()->getSingleScalarResult();

        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('count(r)')->from('AppBundle:BookRating','r')
            ->where($qb->expr()->eq('r.book',':book'));
        $qb->setParameter('book',$book);
        $ratingCount = $qb->getQuery()->getSingleScalarResult();

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('num','num');
        $query = $this->doctrine->getManager()->createNativeQuery('SELECT count(*) as num FROM book_follower WHERE book_id = :book', $rsm);
        $query->setParameter('book', $book->getId());

        $followerCount= $query->getSingleScalarResult();
        return array(
            'ratingCount' => $ratingCount,
            'reviewCount' => $reviewCount,
            'followerCount' => $followerCount
        );
    }

    public function getName()
    {
        return 'app_book_rating';
    }
}
