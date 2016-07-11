<?php

namespace AppBundle\Controller\PrivateSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Review;
use AppBundle\Forms\ReviewType;
use AppBundle\Entity\BookList;

use AppBundle\Forms\BookListType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListController extends Controller
{
    /**
     * @Route("/listas-libros", name="public_lists", defaults={"page" = 1}, options={"expose"=true})
     * @Route("/listas-libros/page/{page}", name="public_lists_paged", defaults={"page" = 1})
     * @Method("GET")
     */
    public function publicListsAction($page, Request $request)
    {

        $searchTerm = $request->get('search');
        $order = $request->get('order');

        if(is_null($order))
            $order = 'follows';

        $em = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();

        $qb->select('l')
            ->from('AppBundle:BookList','l')
            ->where($qb->expr()->gte('l.publicFlag',':public'));

        if(!is_null($searchTerm) && trim($searchTerm) != ""){
            $qb->andWhere($qb->expr()->like('l.name',':searchTerm'));
            $qb->setParameter('searchTerm','%'.$searchTerm.'%');
        }

        $qb->groupBy('l.id');

        if($order == 'follows'){
            $qb->orderBy('l.followersCount','DESC');
        }

        if($order == 'books'){
            $qb->orderBy('l.bookCount','DESC');
        }

        $qb->setParameter('public',BookList::READ_PUBLIC);

        $query = $qb->getQuery();

        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            20/*limit per page*/
        );

        $pagination->setUsedRoute('public_lists_paged');
        $pagination->setTemplate('AppBundle:public:pagination.html.twig');
        $response = $this->render('AppBundle:list:public_lists.html.twig',array('pagination' => $pagination, 'searchTerm' => $searchTerm, 'order' => $order));

        $response->setPublic();
        $response->setSharedMaxAge(600);

        return $response;
    }

    /**
     * @Route("/add_to_list", name="add_to_list", options={"expose"=true})
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function addToListAction(Request $request)
    {
        $list = $this->get('app.list')->processAddition($request,$this->getUser());
        if($list){
            $this->get('doctrine')->getManager()->flush();
            return new JsonResponse(array('status' => 'OK','list' => $list->getId()));
        }
        else
            return new JsonResponse(array('status' => 'KO'),400);
    }

    /**
     * @Route("/delete_list/{id}", name="delete_list", options={"expose"=true})
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteListAction($id,Request $request)
    {
        $request = $this->getRequest();
        $list = $this->get('doctrine')->getRepository('AppBundle:BookList')->findOneBy(array('id' => $id));

        if(is_null($list))
            throw $this->createNotFoundException('No se ha encontrado la lista');

        $user = $this->getUser();

        if($list->getUser() != $user)
            throw $this->createNotFoundException('No se ha encontrado la lista');

        $this->get('app.list')->deleteList($list);

        $this->get('doctrine')->getManager()->flush();

        return $this->redirectToRoute('my_lists');
    }

    /**
     * @Route("/remove_from_list", name="remove_from_list", options={"expose"=true})
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     */
    public function removeFromListAction(Request $request)
    {
        if($this->get('app.list')->processRemove($request,$this->getUser())){
            $this->get('doctrine')->getManager()->flush();
            return new JsonResponse(array('status' => 'OK'));
        }
        else
            return new JsonResponse(array('status' => 'KO'),400);
    }

    /**
     * @Route("/edit_list/{id}", name="edit_list", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     */
    public function editListAction($id, Request $request)
    {
        $request = $this->getRequest();
        $list = $this->get('doctrine')->getRepository('AppBundle:BookList')->findOneBy(array('id' => $id));

        if(is_null($list))
            throw $this->createNotFoundException('No se ha encontrado la lista');

        $user = $this->getUser();

        if($list->getUser() != $user)
            throw $this->createNotFoundException('No se ha encontrado la lista');

        $form = $this->createForm(new BookListType(),$list);

        if($request->getMethod() == 'POST'){
            $form->handleRequest($request);
            if($form->isValid()){
                $this->getDoctrine()->getManager()->persist($list);

                if($list->getPublicFlag() < BookList::READ_PUBLIC)
                    $list->getFollowers()->clear();

                $this->getDoctrine()->getManager()->flush();
                return $this->redirect($this->generateUrl('list_page',array('slug' => $list->getSlug())));
            }
        }

        $response = $this->render('AppBundle:list:edit_list.html.twig',array('list' => $list, 'form' => $form->createView()));

        return $response;
    }

    /**
     * @Route("/my_lists", name="my_lists", options={"expose"=true})
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function getMyListsAction(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            return $this->render('AppBundle:list:user_lists.html.twig',array('lists' => $this->getUser()->getLists()));
        else{
            $serializer = $this->get('jms_serializer');
            $response = new Response($serializer->serialize($this->getUser()->getLists(),'json'));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    /**
     * @Route("/popular_lists_timeline", name="popular_lists_timeline", options={"expose"=true})
     * @Method("GET")
     */
    public function popularListsAction()
    {
        $lists = $this->get('app.popular')->popularLists();
        $response = $this->render('AppBundle:logged:timeline\popular_lists.html.twig',array('lists' => $lists));

        $response->setPublic();
        $response->setSharedMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/lista/{slug}", name="list_page", options={"expose"=true})
     * @Method("GET")
     */
    public function listPageAction($slug, Request $request)
    {
        $request = $this->getRequest();
        $list = $this->get('doctrine')->getRepository('AppBundle:BookList')->findOneBy(array('slug' => $slug));

        if(is_null($list))
            throw $this->createNotFoundException('No se ha encontrado la lista');

        $user = $this->getUser();

        if($list->getPublicFlag() < BookList::READ_PUBLIC && $list->getUser() != $user)
            throw $this->createNotFoundException('No se ha encontrado la lista');

        $response = $this->render('AppBundle:list:list_page.html.twig',array('list' => $list));

        return $response;
    }

    /**
     * @Route("/books_in_list/{id}", name="books_in_list", options={"expose":true})
     * @Method("GET")
     */
    public function booksInListAction($id, Request $request)
    {
        $request = $this->getRequest();
        $list = $this->get('doctrine')->getRepository('AppBundle:BookList')->findOneBy(array('id' => $id));

        if(is_null($list))
            throw $this->createNotFoundException('No se ha encontrado la lista');

        $user = $this->getUser();

        if($list->getPublicFlag() < BookList::READ_PUBLIC && $list->getUser() != $user)
            throw $this->createNotFoundException('No se ha encontrado la lista');

        $offset = $request->get('offset');
        $count = $request->get('count');
        $books = $this->get('app.list')->booksInList($list,$offset,$count);

        $response = $this->render('AppBundle:list:books_in_list.html.twig',array('list' => $list, 'books' => $books));
        return $response;
    }

    /**
     * @Route("/list_last_comments/{id}", name="list_last_comments")
     */
    public function listLastCommentsAction($id, Request $request)
    {
        $list = $this->get('doctrine')->getRepository('AppBundle:BookList')->findOneBy(array('id' => $id));

        if(is_null($list))
            throw $this->createNotFoundException('No se ha encontrado la lista');

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT c FROM AppBundle:Comment c where c.list = :list order by c.created  DESC";
        $query = $em->createQuery($dql);
        $query->setParameters(array('list' => $list));
        $query->setMaxResults(3);

        $comments = $query->getResult();

        $response = $this->render('AppBundle:public:element_last_comments.html.twig',
            array('comments' => $comments,'element' => $list, 'type' => 'list'));

        $response->setETag(md5($response->getContent()));
        $response->setPublic();
        $response->isNotModified($request);

        return $response;
    }

    /**
     * @Route("/list_followers_tab/{id}", name="list_followers_tab")
     */
    public function listFollowersTabAction($id, Request $request)
    {
        $list = $this->get('doctrine')->getRepository('AppBundle:BookList')->findOneBy(array('id' => $id));

        if(is_null($list))
            throw $this->createNotFoundException('No se ha encontrado la lista');

        $response = $this->render('AppBundle:list:followers_tab.html.twig',array('list' => $list));
        $response->setETag(md5($response->getContent()));
        $response->setPublic();
        $response->isNotModified($request);

        return $response;
    }

}
