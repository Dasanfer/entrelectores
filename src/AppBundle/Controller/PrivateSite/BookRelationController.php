<?php

namespace AppBundle\Controller\PrivateSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Book;
use AppBundle\Entity\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\JsonResponse;

class BookRelationController extends Controller
{
    /**
     * @Route("/user_book_relation/{id}", name="user_book_relation", options={"expose" = true})
     */
    public function userBookRelationAction($id,Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))         {
            return new JsonResponse(array());
        }

        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('id' => $id));
        if(is_null($book))
            throw $this->createNotFoundException();

        $user = $this->getUser();
        $rel = $this->get('app.book_relation');
        return new JsonResponse(
            array(
                'reading' => $rel->isReading($user,$book),
                'read' => $rel->hasRead($user,$book),
                'wants' => $rel->wantsToRead($user,$book)
            )
        );
    }


    /**
     * @Route("/user_add_reading", name="user_add_reading", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     * @Method("POST")
     */
    public function addReadingAction(Request $request)
    {
        $user = $this->getUser();
        $bookId = $request->request->get('book');

        if(is_null($bookId))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneById($bookId);

        if(is_null($book))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $this->get('app.book_relation')->addReading($user,$book);
        $this->get('doctrine')->getManager()->flush();

        return new JsonResponse(array('status' => 'OK'));
    }

    /**
     * @Route("/user_add_read", name="user_add_read", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     * @Method("POST")
     */
    public function addReadAction(Request $request)
    {
        $user = $this->getUser();
        $bookId = $request->request->get('book');

        if(is_null($bookId))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneById($bookId);

        if(is_null($book))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $this->get('app.book_relation')->addRead($user,$book);
        $this->get('doctrine')->getManager()->flush();

        return new JsonResponse(array('status' => 'OK'));
    }

    /**
     * @Route("/user_add_want", name="user_add_want", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     * @Method("POST")
     */
    public function addWantAction(Request $request)
    {
        $user = $this->getUser();
        $bookId = $request->request->get('book');

        if(is_null($bookId))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneById($bookId);

        if(is_null($book))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $this->get('app.book_relation')->addWant($user,$book);
        $this->get('doctrine')->getManager()->flush();

        return new JsonResponse(array('status' => 'OK'));
    }

    /**
     * @Route("/book_relation_delete/{id}", name="book_relation_delete", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     * @Method("DELETE")
     */
    public function bookRelationDeleteAction($id,Request $request)
    {
        $user = $this->getUser();
        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneById($id);

        if(is_null($book))
            throw $this->createNotFoundException('No se ha encontrado el libro');

        $this->get('app.book_relation')->deleteRelation($user,$book);
        $this->get('doctrine')->getManager()->flush();

        return new JsonResponse(array('status' => 'OK'));
    }

    /**
     * @Route("/mis_libros", name="my_books")
     * @Security("has_role('ROLE_USER')")
     */
    public function myBooksAction(Request $request){
        $user = $this->getUser();
        return $this->render('AppBundle:user:all_books.html.twig',array('user' => $user));
    }

    /**
     * @Route("/user_readed/{userId}/{offset}/{count}", name="my_readed_books", options={"expose"=true})
     */
    public function myReadedBooksAction($userId,$offset,$count,Request $request){
        $user = $this->get('doctrine')->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
        if(is_null($user)){
            throw $this->createNotFoundException();
        }
        if(!$user->getPublicProfile() && $user != $this->getUser()){
            throw new AccessDeniedException();
        }
        $query = $this->get('app.book_relation')->readBooksQuery($user);
        $query->setMaxResults($count)->setFirstResult($offset);

        $response = $this->render('AppBundle:user:books_readed.html.twig',
            array(
                'books' => $query->getResult(),
                'owner' => $user == $this->getUser()
            )
        );

        return $response;
    }

    /**
     * @Route("/user_wanted/{userId}/{offset}/{count}", name="my_wanted_books", options={"expose"=true})
     */
    public function myWantedBooksAction($userId,$offset,$count,Request $request){
        $user = $this->get('doctrine')->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
        if(is_null($user)){
            throw $this->createNotFoundException();
        }
        if(!$user->getPublicProfile() && $user != $this->getUser()){
            throw new AccessDeniedException();
        }

        $query = $this->get('app.book_relation')->wantsBooksQuery($user);
        $query->setMaxResults($count)->setFirstResult($offset);
        $response = $this->render('AppBundle:user:books_readed.html.twig',
            array(
                'books' => $query->getResult(),
                'owner' => $user == $this->getUser()
            )
        );

        return $response;
    }

    /**
     * @Route("/user_reading/{userId}/{offset}/{count}", name="my_reading_books", options={"expose" = true})
     */
    public function myReadingBooksAction($userId,$offset,$count,Request $request){
        $user = $this->get('doctrine')->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
        if(is_null($user)){
            throw $this->createNotFoundException();
        }
        if(!$user->getPublicProfile() && $user != $this->getUser()){
            throw new AccessDeniedException();
        }

        $query = $this->get('app.book_relation')->readingBooksQuery($this->getUser());
        $query->setMaxResults($count)->setFirstResult($offset);
        $response = $this->render('AppBundle:user:books_readed.html.twig',
            array(
                'books' => $query->getResult(),
                'owner' => $user == $this->getUser()
            )
        );
        return $response;
    }

    private function arbitraryListPagination($query,$page){

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            20/*limit per page*/
        );
        $pagination->setTemplate('AppBundle:public:pagination.html.twig');
        return $pagination;
    }


}
