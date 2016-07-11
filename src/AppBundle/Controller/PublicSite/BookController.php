<?php

namespace AppBundle\Controller\PublicSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

use AppBundle\Forms\ReviewType;
use AppBundle\Entity\Review;
use AppBundle\Entity\BookList;

class BookController extends Controller
{
    /**
     * @Route("/bookspromoted", name="bookspromoted")
     */
    public function promotedAction()
    {
        $books = $this->get('app.promoted')->getPromoted(3);
        $response = $this->render('AppBundle:public:books_promoted.html.twig',array('books' => $books));

        $response->setPublic();
        $response->setSharedMaxAge(600);
        return $response;
    }

    /**
     * @Route("/books_sidebar_promoted", name="books_sidebar_promoted")
     */
    public function promotedSidebarAction()
    {
        $books = $this->get('app.promoted')->getPromoted(3);
        $response = $this->render('AppBundle:book:sidebar_promoted.html.twig',array('books' => $books));
        $response->setPublic();
        $response->setSharedMaxAge(600);
        return $response;
    }
    /**
     * @Route("/libros", name="allbooks", defaults={"page" = 1})
     * @Route("/libros/page/{page}", name="allbooks_paged", defaults={"page" = 1})
     */
    public function allBookAction($page, Request $request)
    {
        $order = $request->get('order');
        $em    = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();

        $qb->select('b')->from('AppBundle:Book','b');

        if($order == 'mark')
            $qb->orderBy('b.cachedRate','DESC');
        elseif($order == 'reviews')
            $qb->orderBy('b.reviewCount','DESC');
        elseif($order == 'rating')
            $qb->orderBy('b.ratingCount','DESC');
        else
            $qb->orderBy('b.followersCount','DESC');

        $query = $qb->getQuery();
        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            40/*limit per page*/
        );

        $pagination->setUsedRoute('allbooks_paged');
        $pagination->setTemplate('AppBundle:public:pagination.html.twig');
        $response = $this->render('AppBundle:book:all_books_page.html.twig',array('pagination' => $pagination));

        $response->setPublic();
        $response->setSharedMaxAge(3600);

        return $response;
    }



    /**
     * @Route("/libros/{authorslug}/{slug}", name="bookpage")
     */
    public function bookAction($authorslug,$slug)
    {
        if($authorslug == 'detalle'){
            return $this->forward('app_controller.migration:oldBookDetailAction',array('slug' => $slug));
        }

        $securityContext = $this->container->get('security.context');
        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('slug' => $slug));
        $author = $this->get('doctrine')->getRepository('AppBundle:Author')->findOneBy(array('slug' => $authorslug));

        if(is_null($book) || is_null($author) || $author != $book->getAuthor())
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $reviewForm = null;

        if($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            $user = $this->getUser();

            if(!$this->get('app.review')->reviewExists($user,$book))
                $reviewForm = $this->createForm(new ReviewType(), new Review(),['em' => $this->get('doctrine')->getManager()]);
        }

        $response = $this->render(
            'AppBundle:book:book_page.html.twig',
            array(
                'book' => $book,
                'reviewForm' => is_null($reviewForm) ? null : $reviewForm->createView()
            )
        );

        return $response;
    }

    /**
     * @Route("/busqueda", name="search_books", defaults={"page" = 1})
     * @Route("/busqueda/{page}", name="search_books_paged", defaults={"page" = 1})
     */
    public function searchBookAction($page,Request $request)
    {
        $term = $request->get('term');

        if(is_null($term)){
            return new RedirectResponse($this->generateUrl('allbooks'));
        }
        $results = $this->get('app.search')->getSearchArray($term);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $results,
            $page/*page number*/,
            20/*limit per page*/
        );

        $pagination->setParam('term',$term);
        $pagination->setUsedRoute('search_books_paged');
        $pagination->setTemplate('AppBundle:public:pagination.html.twig');
        $response = $this->render('AppBundle:public:search_result.html.twig',array('pagination' => $pagination));

        $response->setPublic();
        $response->setSharedMaxAge(600);
        return $response;
    }

    /**
     * @Route("/booksuggest_sidebar/{id}", name="book_suggest_sidebar")
     */
    public function bookSuggestSidebarContentAction($id){
        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('id' => $id));

        if(is_null($book))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $books = $this->get('app.suggestion')->getSuggestionsFromBook($book);

        $response = $this->render('AppBundle:book:book_sidebar_suggest.html.twig',array('book' => $book,'books' => $books));

        $response->setPublic();
        $response->setSharedMaxAge(3600);
        return $response;

    }

    /**
     * @Route("/booksuggest_user_sidebar/{id}/{userid}", name="book_suggest_user_sidebar")
     * @Security("has_role('ROLE_USER')")
     */
    public function bookUserSuggestSidebarContentAction($id,$userid){
        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('id' => $id));
        $user = $this->get('doctrine')->getRepository('AppBundle:User')->findOneBy(array('id' => $userid));

        if(is_null($book) || is_null($user))
            throw $this->createNotFoundException('No se ha encontrado el libro/usuario');

        $books = $this->get('app.suggestion')->getSuggestionsFromBookAndUser($book,$user);

        $response = $this->render('AppBundle:book:book_sidebar_suggest.html.twig',array('books' => $books, 'book' => $book));

        $response->setPublic();
        $response->setSharedMaxAge(60);
        return $response;
    }


    /**
     * @Route("/bookmain/{id}", name="book_main_data")
     */
    public function bookMainContentAction($id){
        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('id' => $id));

        if(is_null($book))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $response = $this->render('AppBundle:book:book_main_data.html.twig',array('book' => $book));

        return $response;

    }


    /**
     * @Route("/fromsameauthor/{id}", name="from_same_author")
     */
    public function fromSameAuthorAction($id)
    {
        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('id' => $id));
        if(is_null($book))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT b FROM AppBundle:Book b where b.author = :author and b != :book";
        $query = $em->createQuery($dql);

        $query->setParameters(array('book' => $book, 'author' => $book->getAuthor()));
        $query->setMaxResults(3);
        $books = $query->getResult();

        $response = $this->render('AppBundle:book:author_list.html.twig',array('author' => $book->getAuthor(), 'books' => $books));

        $response->setPublic(true);
        $response->setSharedMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/book_in_lists/{id}", name="book_in_lists")
     */
    public function bookInListsAction($id)
    {
        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('id' => $id));
        if(is_null($book))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT l FROM AppBundle:BookList l join l.books b where b = :book and l.publicFlag = :flag ";

        $query = $em->createQuery($dql);

        $query->setParameters(array('book' => $book,'flag' => BookList::READ_PUBLIC));
        $query->setMaxResults(3);
        $lists = $query->getResult();

        $response = $this->render('AppBundle:book:lists_book_appears.html.twig',array('lists' => $lists,'book' => $book));

        $response->setPublic(true);
        $response->setSharedMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/book_last_comments/{id}", name="book_last_comments")
     */
    public function bookLastCommentsAction($id)
    {
        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('id' => $id));
        if(is_null($book))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT c FROM AppBundle:Comment c where c.book = :book order by c.created  DESC";
        $query = $em->createQuery($dql);
        $query->setParameters(array('book' => $book));
        $query->setMaxResults(3);

        $comments = $query->getResult();

        $response = $this->render('AppBundle:public:element_last_comments.html.twig',
            array('comments' => $comments,'element' => $book, 'type' => 'book'));

        $response->setPublic(true);
        $response->setSharedMaxAge(600);

        return $response;
    }

    /**
     * @Route("/book_best_reviews/{id}", name="book_best_reviews")
     */
    public function bookBestReviewAction($id)
    {
        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('id' => $id));
        if(is_null($book))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT c FROM AppBundle:Review c where c.book = :book and ( c.spoiler = false or c.spoiler is null) order by c.cachedRate DESC";
        $query = $em->createQuery($dql);
        $query->setParameters(array('book' => $book));
        $query->setMaxResults(1);

        $reviews = $query->getResult();

        $response = $this->render('AppBundle:book:book_best_reviews.html.twig',
            array('book' => $book,'reviews' => $reviews));

        $response->setPublic(true);
        $response->setSharedMaxAge(600);

        return $response;
    }
    /**
     * @Route("/popular_books_timeline", name="popular_books_timeline", options={"expose"=true})
     * @Method("GET")
     */
    public function popularBooksAction()
    {
        $books = $this->get('app.popular')->popularBooks();
        $response = $this->render('AppBundle:logged:timeline\popular_books.html.twig',array('books' => $books));
        $response->setPublic(true);
        $response->setSharedMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/books_sidebar_novelty", name="books_sidebar_novelty")
     * @Method("GET")
     */
    public function noveltyBooksSidebarAction()
    {
        $books = $this->get('app.promoted')->getNovelty();
        $response = $this->render('AppBundle:book:novelty_sidebar.html.twig',array('books' => $books));
        $response->setPublic(true);
        $response->setSharedMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/book_page_book_stats/{id}", name="book_page_book_stats")
     * @Method("GET")
     */
    public function bookPageBookStatsAction($id)
    {
        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneById($id);
        if(is_null($book))
            throw $this->createNotFoundException();

        $response = $this->render('AppBundle:book:book_stats.html.twig',array('book' => $book));
        $response->setPublic(true);
        $response->setSharedMaxAge(600);
        return $response;
    }
}
