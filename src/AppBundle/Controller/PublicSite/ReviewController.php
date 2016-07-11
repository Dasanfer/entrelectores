<?php

namespace AppBundle\Controller\PublicSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Review;
use AppBundle\Forms\ReviewType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\FollowEvent;
use AppBundle\Services\Timeline\TimelineEvent;

class ReviewController extends Controller
{
    /**
     * @Route("/review", name="post_review", options={"expose"=true})
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function postReviewAction(Request $request)
    {
        $review = new Review();
        $form = $this->createForm(new ReviewType(),$review,['em' => $this->get('doctrine')->getManager()]);
        $serializer = $this->get('jms_serializer');
        $form->handleRequest($request);
        $user = $this->getUser();

        if($form->isValid()){
            $review->setUser($user);

            $event = new TimelineEvent($user,FollowEvent::review,$review->getBook());
            $event->setReview($review);

            $this->get('event_dispatcher')->dispatch('app.timeline_event',$event);
            $review->getBook()->setUpdated(new \DateTime());
            $this->get('doctrine')->getManager()->persist($review);
            $this->get('doctrine')->getManager()->persist($review->getBook());
            $this->get('doctrine')->getManager()->flush();

            $response =  new JsonResponse(array('id' => $review->getId()),201);
            $response->headers->set('Content-Type', 'application/json');
        } else {
            $formData = $serializer->serialize($form->getErrors(), 'json');
            $response =  new Response($formData,400);
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }

    /**
     * @Route("/bookreviews/{id}/{offset}/{count}", name="book_reviews", defaults={"offset"=0,"count" = 10})
     */
    public function bookReviewsAction($id,$offset=0,$count = 10){
        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('id' => $id));

        if(is_null($book))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $reviews = $this->get('app.review')->bookReviews($book,$offset,$count);

        $response = $this->render('AppBundle:book:book_reviews.html.twig',array('reviews' => $reviews));
        return $response;
    }

    /**
     * @Route("/ajaxbookreviews/{id}/{offset}", name="ajax_book_review", defaults={"offset" = 0}, options={"expose"=true})
     * @Method("GET")
     */
    public function ajaxBookReviewsAction($id,$offset,Request $request)
    {
        $serializer = $this->get('jms_serializer');
        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('id' => $id));
        if(is_null($book))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $reviews = $this->get('app.review')->bookReviews($book, $offset, 10);

        $response = $this->render('AppBundle:book:book_reviews.html.twig',array('reviews' => $reviews,'book' => $book));
        $response->headers->set('total',$book->getReviews()->count());

        return $response;
    }

    /**
     * @Route("/los-mas-votados/mes/{year}/{month}", name="most_voted_month", requirements={
     *     "year": "\d+",
     *     "month": "\d+"
     * })
     */
    public function mostVotedMonthAction($year,$month)
    {
        $begin = new \DateTime($year.'-'.$month.'-00');
        $end = new \DateTime($year.'-'.$month.'-31');

        $books = $this->get('app.rating')->mostVoted($begin,$end,20);
        $response = $this->render('AppBundle:book:most_voted.html.twig',array('books' => $books, 'title' => 'Libros más votados del més '.$month.' de '.$year));
        $response->setPublic();
        $response->setSharedMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/los-mas-votados/semana/{year}/{week}", name="most_voted_week", requirements={
     *     "year": "\d+",
     *     "week": "\d+"
     * })
     */
    public function mostVotedWeekAction($year,$week)
    {
        $week = sprintf("%02u", $week);//prepend 0
        $begin = new \DateTime(); // First day of week
        $end = new \DateTime();
        $begin->setTimestamp(strtotime($year.'W'.$week.'1'));
        $end->setTimestamp(strtotime($year.'W'.$week.'7'));
        $books = $this->get('app.rating')->mostVoted($begin,$end,20);
        $response = $this->render('AppBundle:book:most_voted.html.twig',array('books' => $books, 'title' => 'Libros más votados de la semana '.$week.' de '.$year));
        $response->setPublic();
        $response->setSharedMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/los-mas-votados", name="most_voted")
     */
    public function mostVotedYearAction()
    {
        $begin = new \DateTime();
        $end = new \DateTime();
        $begin->modify('-1 year');
        $books = $this->get('app.rating')->mostVoted($begin,$end,20);
        $response = $this->render('AppBundle:book:most_voted.html.twig',array('books' => $books));
        $response->setPublic();
        $response->setSharedMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/rate_book/{id}", name="rate_book", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     * @Method("POST")
     */
    public function bookRateAction($id, Request $request)
    {
        $value = $request->request->get('value');
        if(is_null($value))
            return new Response('No value',400);

        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneById($id);

        if(is_null($book))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $user = $this->getUser();

        $this->get('app.rating')->newBookRating($user,$book,$value);
        $this->get('app.book_relation')->addRead($user,$book);

        $this->get('doctrine')->getManager()->flush();

        return new Response('OK');
    }

    /**
     * @Route("/most_voted_sidebar/{count}", name="most_voted_sidebar")
     */
    public function mostVotedSidebarAction($count)
    {
        $beginmonth = new \DateTime(date('Y'.'-'.date('m').'-01'));
        $endmonth = new \DateTime(date('Y'.'-'.date('m').'-31'));
        $beginweek = new \DateTime(date("Y-m-d", strtotime( date( 'Y-m-d' )." -7 days")));
        $endweek = new \DateTime();

        $booksmonth = $this->get('app.rating')->mostVoted($beginmonth,$endmonth,$count);
        $booksweek = $this->get('app.rating')->mostVoted($beginweek,$endweek,$count);

        $response = $this->render('AppBundle:book:most_voted_sidebar.html.twig',
            [
                'booksmonth' => $booksmonth,
                'booksweek' => $booksweek,
                'year' => date('Y'),
                'month' => date('m'),
                'week' => date('W')
            ]);

        $response->setPublic();
        $response->setSharedMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/last_book_sidebar/", name="last_book_sidebar")
     */
    public function lastBookSidebarAction()
    {

        $qb = $this->get('doctrine')->getManager()->createQueryBuilder();
        $qb->select('e as Post')->from('AppBundle:Post','e');
        $qb->orderBy('e.created','DESC');
        $qb->setMaxResults(3);
        $result = $qb->getQuery()->getResult();

        $response = $this->render('AppBundle:list:lasts_post_blog.html.twig', 
                                   array('posts' => $result)
        );
        
        return $response;
    }
    
        /**
     * @Route("/categories_sidebar/", name="categories_sidebar")
     */
    public function categoriesSidebarAction()
    {

        $em = $this->getDoctrine()->getManager();
        $rep   = $em->getRepository('AppBundle:PostCategory');
        $query = $rep->createQueryBuilder('c')
                     ->select()
                     ->getQuery();

        $result = $query->getArrayResult();

        $response = $this->render('AppBundle:list:categories-post.html.twig', 
                                   array('categories' => $result)
        );
        
        return $response;
    }

    /**
     * @Route("/review/rate_up/{id}", name="review_rate_up", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     * @Method("POST")
     */
    public function reviewRateUpAction($id)
    {
        $review = $this->get('doctrine')->getRepository('AppBundle:Review')->findOneBy(array('id' => $id));

        if(is_null($review))
            throw $this->createNotFoundException('No se ha encontrado la reseña');

        $this->get('app.rating')->newReviewRating($review,$this->getUser(),1);
        $this->get('doctrine')->getManager()->flush();

        return new JsonResponse($this->get('app.rating')->getReviewRatings($review));
    }


    /**
     * @Route("/libro/{bookSlug}/resena/{id}", name="single_review")
     */
    public function singleReviewAction($bookSlug,$id)
    {
        $review = $this->get('doctrine')->getRepository('AppBundle:Review')->findOneBy(array('id' => $id));

        if(is_null($review) || $review->getBook()->getSlug() != $bookSlug)
            throw $this->createNotFoundException('No se ha encontrado la reseña');

        return $this->render('AppBundle:book:single_review.html.twig',array('review' => $review));
    }

    /**
     * @Route("/review/rate_down/{id}", name="review_rate_down", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     * @Method("POST")
     */
    public function reviewRateDownAction($id)
    {
        $review = $this->get('doctrine')->getRepository('AppBundle:Review')->findOneBy(array('id' => $id));

        if(is_null($review))
            throw $this->createNotFoundException('No se ha encontrado la reseña');

        $this->get('app.rating')->newReviewRating($review,$this->getUser(),-1);
        $this->get('doctrine')->getManager()->flush();

        return new JsonResponse($this->get('app.rating')->getReviewRatings($review));
    }

}
