<?php

namespace AppBundle\Controller\PublicSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthorController extends Controller
{
    /**
     * @Route("/autores/page/{page}", name="authorspaged")
     * @Route("/autores", name="authors", defaults={"page" = 1})
     * @Route("/autores/", name="authors_slash", defaults={"page" = 1})
     */
    public function authorsAction($page)
    {
        $request = $this->getRequest();

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM AppBundle:Author a order by a.followersCount DESC";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            40/*limit per page*/
        );

        $pagination->setUsedRoute('authorspaged');
        $pagination->setTemplate('AppBundle:public:pagination.html.twig');
        $response = $this->render('AppBundle:author:authors_list.html.twig',array('pagination' => $pagination));

        $response->setPublic();
        $response->setSharedMaxAge(600);
        return $response;
    }

    /**
     * @Route("/autores/{slug}", name="authorpage")
     */
    public function authorAction($slug)
    {
        $request = $this->getRequest();
        $author = $this->get('doctrine')->getRepository('AppBundle:Author')->findOneBy(array('slug' => $slug));

        if(is_null($author)){
            $author = $this->get('doctrine')->getRepository('AppBundle:Author')->findOneBy(array('oldSlug' => $slug));
            if(is_null($author)){
                throw $this->createNotFoundException('No se ha encontrado el autor');
            } else {
                return $this->redirectToRoute('authorpage',array('slug' => $author->getSlug()),301);
            }
        }

        $response = $this->render('AppBundle:author:author_page.html.twig',array('author' => $author));

        return $response;
    }

    /**
     * @Route("/author_last_comments/{id}", name="author_last_comments")
     */
    public function authorLastCommentsAction($id)
    {
        $author = $this->get('doctrine')->getRepository('AppBundle:Author')->findOneBy(array('id' => $id));

        if(is_null($author))
            throw $this->createNotFoundException('No se ha encontrado el autor');

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT c FROM AppBundle:Comment c where c.author = :author order by c.created  DESC";
        $query = $em->createQuery($dql);
        $query->setParameters(array('author' => $author));
        $query->setMaxResults(3);

        $comments = $query->getResult();

        $response = $this->render('AppBundle:public:element_last_comments.html.twig',
            array('comments' => $comments,'element' => $author, 'type' => 'author'));

        $response->setPublic();
        $response->setSharedMaxAge(600);

        return $response;
    }

    /**
     * @Route("/popular_authors_timeline", name="popular_authors_timeline", options={"expose"=true})
     * @Method("GET")
     */
    public function popularAuthorsAction()
    {
        $authors = $this->get('app.popular')->popularAuthors();
        $response = $this->render('AppBundle:logged:timeline\popular_authors.html.twig',array('authors' => $authors));

        $response->setPublic();
        $response->setSharedMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/author_books/{authorId}/{offset}/{count}", name="author_books", options={"expose"=true})
     * @Method("GET")
     */
    public function authorBooksAction($authorId,$offset,$count)
    {
        $qb = $this->get('doctrine')->getManager()->createQueryBuilder();
        $qb->select('b')->from('AppBundle:Book','b')->join('b.author','a')
            ->where($qb->expr()->eq('a.id',':author'))->orderBy('b.created','DESC');
        $qb->setParameter('author',$authorId);

        $books = $qb->getQuery()->setMaxResults($count)->setFirstResult($offset)->getResult();
        $response = $this->render('AppBundle:author:author_books.html.twig',array('books' => $books));
        $response->setPublic();
        $response->setSharedMaxAge(3600);
        return $response;
    }
}
