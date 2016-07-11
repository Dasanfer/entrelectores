<?php

namespace AppBundle\Controller\PrivateSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Review;
use AppBundle\Entity\UserStatus;
use AppBundle\Forms\ReviewType;
use AppBundle\Forms\UserType;
use AppBundle\Forms\UserStatusType;
use AppBundle\Forms\ChangePasswordType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{

    /**
     * @Route("/user_homepage", name="user_homepage")
     * @Security("has_role('ROLE_USER')")
     */
    public function userHomepageAction(Request $request)
    {
        $statusForm = $this->createForm(new UserStatusType());
        $response = $this->render('AppBundle:logged:homepage.html.twig',array('statusForm' => $statusForm->createView()));

        return $response;
    }

    /**
     * @Route("/user/{slug}", name="user_profile")
     */
    public function userProfileAction($slug,Request $request){
        $user = $this->get('doctrine')->getRepository('AppBundle:User')->findOneBy(array('slug' => $slug));

        if(is_null($user)){
            throw $this->createNotFoundException();
        }

        if($user->getPublicProfile() || $user == $this->getUser()){
            $response = $this->render('AppBundle:user:profile.html.twig',array('user' => $user));
        }
        else {
            $response = $this->render('AppBundle:user:private_profile.html.twig',array('user' => $user));
        }

        return $response;
    }

    /**
     * @Route("/user_followers/{id}", name="user_followers")
     */
    public function userFollowersAction($id,Request $request){
        $user = $this->get('doctrine')->getRepository('AppBundle:User')->findOneBy(array('id' => $id));

        if(is_null($user)){
            throw $this->createNotFoundException();
        }

        $response = $this->render('AppBundle:user:followers_tab.html.twig',array('user' => $user,'followers' => $user->getFollowers()));
        $response->setSharedMaxAge(600);

        return $response;
    }

    /**
     * @Route("/user_public_lists/{id}", name="user_public_lists")
     */
    public function userPublicListsAction($id,Request $request){
        $user = $this->get('doctrine')->getRepository('AppBundle:User')->findOneBy(array('id' => $id));

        if(is_null($user)){
            throw $this->createNotFoundException();
        }

        $response = $this->render('AppBundle:user:user_public_lists.html.twig',array('lists' => $this->get('app.book_relation')->getUserPublicLists($user),'user' => $user));

        return $response;
    }

    /**
     * @Route("/user_timeline/{offset}", name="user_timeline", defaults={"offset" = 0}, options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     */
    public function userTimelineAction(Request $request,$offset = 0)
    {
        $events = $this->get('app.user.timeline')->userTimeline($this->getUser(),$offset,10);
        $total =  $this->get('app.user.timeline')->userTimelineCount($this->getUser());
        $response = $this->render('AppBundle:logged:timeline/user_timeline.html.twig'
                ,array('events' => $events,'offset' => $offset)
            );
        $response->headers->set('total',$total);
        return $response;
    }

    /**
     * @Route("/user_follow_objects", name="user_follow_objects",options={"expose"=true})
     */
    public function userFollowObjectsAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))         {
            return new JsonResponse(array());
        }

        $serializer = $this->get('jms_serializer');
        $user = $this->getUser();

        $data = array(
            'lists_followed' => $user->getListsFollowed(),
            'users_followed' => $user->getUsersFollowed(),
            'authors_followed' => $user->getAuthorsFollowed(),
            'books_followed' => $user->getBooksFollowed()
        );

        $response = new Response($serializer->serialize($data,'json',SerializationContext::create()->setGroups(array('follows'))));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/user_actions/{slug}/{offset}", name="user_actions", defaults={"offset" = 0}, options={"expose"=true})
     */
    public function userActionsAction(Request $request,$slug,$offset = 0)
    {
        $user = $this->get('doctrine')->getRepository('AppBundle:User')->findOneBy(array('slug' => $slug));

        if(is_null($user)){
            throw $this->createNotFoundException();
        }

        $events = $this->get('app.user.timeline')->userActivity($user,$offset,10);

        $response = $this->render('AppBundle:logged:timeline/user_timeline.html.twig',array('events' => $events,'offset' => $offset)
        );
        return $response;
    }

    /**
     * @Route("/user_sidebar_suggestions", name="user_sidebar_suggestions")
     * @Security("has_role('ROLE_USER')")
     */
    public function userSidebarSuggestionsAction(Request $request)
    {
        $books = $this->get('app.suggestion')->getSuggestionsFromUser($this->getUser());
        $response = $this->render('AppBundle:logged:user_sidebar_suggestions.html.twig',array('books' => $books));

        $response->setPrivate();
        $response->setMaxAge(60);
        return $response;
    }

    /**
     * @Route("/user_profile_edit", name="user_profile_edit")
     * @Security("has_role('ROLE_USER')")
     */
    public function userProfileEditAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->get('form.factory')->createNamed('user_profile',new UserType(),$user);
        $formPass = $this->get('form.factory')->createNamed('user_password',new ChangePasswordType(),$user);

        if($request->getMethod() == 'POST'){
            if($request->request->has('user_profile')){
                $form->handleRequest($request);
                if($form->isValid()){

                    if(!$user->getPublicProfile())
                        $user->getFollowers()->clear();

                    $this->get('fos_user.user_manager')->updateUser($user);
                    return $this->redirect($this->generateUrl('user_homepage'));
                }
            } else if($request->request->has('user_password')){
                $formPass->handleRequest($request);
                if($formPass->isValid()){
                    $this->get('fos_user.user_manager')->updateUser($user);
                    return $this->redirect($this->generateUrl('user_homepage'));
                }
            }
        }

        $response = $this->render('AppBundle:user:profile_edit.html.twig',array('form' => $form->createView(),'formPass' => $formPass->createView()));
        return $response;
    }

    /**
     * @Route("/user_reviews", name="user_reviews")
     * @Security("has_role('ROLE_USER')")
     */
    public function userReviewsAction(Request $request)
    {
        $user = $this->getUser();
        $reviews = $user->getReviews();
        $response = $this->render('AppBundle:user:reviews.html.twig',array('reviews' => $reviews));
        return $response;
    }

    /**
     * @Route("/post_status", name="post_status",options={"expose":true})
     * @Security("has_role('ROLE_USER')")
     * @Method("POST")
     */
    public function userPostStatusAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');
        $user = $this->getUser();
        $status = new UserStatus();
        $form = $this->createForm(new UserStatusType(),$status);
        $form->handleRequest($request);
        if($form->isValid()){
            $this->get('app.user.status')->attachStatus($user,$status);
            $this->get('doctrine')->getManager()->flush();
            $response = new JsonResponse(array('stauts' => 'OK'),201);
            $response->headers->set('Content-Type', 'application/json');
        } else {
            $formData = $serializer->serialize($form, 'json');
            $response =  new Response($formData,400);
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }

    /**
     * @Route("/listaUsuarios/page/{page}", name="public_users_list_paged")
     * @Route("/listaUsuarios", name="public_users_list", defaults={"page" = 1})
     */
    public function publicUsersListAction($page, Request $request)
    {
        $searchTerm = $request->get('search');
        $em = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();
        $qb->select('a');
        $qb->from('AppBundle:User','a');
        $qb->where($qb->expr()->eq('a.publicProfile',true));
        $qb->orderBy('a.followersCount','DESC');

        if(!is_null($searchTerm)){
            $qb->andWhere($qb->expr()->like('a.username',':searchTerm'));
            $qb->setParameter('searchTerm','%'.$searchTerm.'%');
        }

        $query = $qb->getQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            20/*limit per page*/
        );

        $pagination->setUsedRoute('public_users_list_paged');
        $pagination->setTemplate('AppBundle:public:pagination.html.twig');
        $response = $this->render('AppBundle:user:user_list.html.twig',array('pagination' => $pagination,'searchTerm' => $searchTerm));

        return $response;
    }
    /**
     * @Route("/popular_users_timeline", name="popular_users_timeline", options={"expose"=true})
     * @Method("GET")
     */
    public function popularUsersAction()
    {
        $users = $this->get('app.popular')->popularUsers();
        $response = $this->render('AppBundle:logged:timeline\popular_users.html.twig',array('users' => $users));
        $response->setPublic();
        $response->setSharedMaxAge(600);
        return $response;
    }

    /**
     * @Route("/does_follow_element/{type}/{id}", name="does_follow_element", options={"expose"=true})
     */
    public function doesFollowAction($type,$id,Request $request){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))         {
            return new JsonResponse(array('follows' => false));
        }
        $user = $this->getUser();
        $element = null;
        $timelineService = $this->get('app.user.timeline');

        switch($type){
            case 'book':
                $element = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneById($id);
                break;
            case 'author':
                $element = $this->get('doctrine')->getRepository('AppBundle:Author')->findOneById($id);
                break;
            case 'list':
                $element = $this->get('doctrine')->getRepository('AppBundle:BookList')->findOneById($id);
                break;
            case 'user':
                $element = $this->get('doctrine')->getRepository('AppBundle:User')->findOneById($id);
                break;
        }

        if(is_null($element))
            throw $this->createNotFoundException();

        return new JsonResponse(
            array('follows' => $this->get('app.user.timeline')->doesUserFollow($element, $type,$this->getUser())
            )
        );
    }

    /**
     * @Route("/follow_element/{type}/{id}", name="follow_element", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     * @Method("POST")
     */
    public function followElementAction($type,$id,Request $request)
    {
        $user = $this->getUser();
        $element = null;
        $timelineService = $this->get('app.user.timeline');

        switch($type){
            case 'book':
                $element = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneById($id);
                break;
            case 'author':
                $element = $this->get('doctrine')->getRepository('AppBundle:Author')->findOneById($id);
                break;
            case 'list':
                $element = $this->get('doctrine')->getRepository('AppBundle:BookList')->findOneById($id);
                break;
            case 'user':
                $element = $this->get('doctrine')->getRepository('AppBundle:User')->findOneById($id);
                break;
        }

        if(is_null($element))
            throw $this->createNotFoundException();

        switch($type){
            case 'book':
                $timelineService->followBook($user,$element);
                break;
            case 'author':
                $timelineService->followAuthor($user,$element);
                break;
            case 'list':
                $timelineService->followList($user,$element);
                break;
            case 'user':
                $timelineService->followUser($user,$element);
                break;
        }

        $element->setUpdated(new \DateTime());
        $this->get('doctrine')->getManager()->persist($element);
        $this->get('doctrine')->getManager()->flush();

        return new JsonResponse(array('status' => 'OK'));

    }

    /**
     * @Route("/unfollow_element/{type}/{id}", name="unfollow_element", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     * @Method("POST")
     */
    public function unfollowElementAction($type,$id,Request $request)
    {
        $user = $this->getUser();
        $element = null;
        $timelineService = $this->get('app.user.timeline');

        switch($type){
            case 'book':
                $element = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneById($id);
                break;
            case 'author':
                $element = $this->get('doctrine')->getRepository('AppBundle:Author')->findOneById($id);
                break;
            case 'list':
                $element = $this->get('doctrine')->getRepository('AppBundle:BookList')->findOneById($id);
                break;
            case 'user':
                $element = $this->get('doctrine')->getRepository('AppBundle:User')->findOneById($id);
                break;
        }

        if(is_null($element))
            throw $this->createNotFoundException();

        switch($type){
            case 'book':
                $timelineService->unfollowBook($user,$element);
                break;
            case 'author':
                $timelineService->unfollowAuthor($user,$element);
                break;
            case 'list':
                $timelineService->unfollowList($user,$element);
                break;
            case 'user':
                $timelineService->unFollowUser($user,$element);
                break;
        }

        $element->setUpdated(new \DateTime());
        $this->get('doctrine')->getManager()->persist($element);
        $this->get('doctrine')->getManager()->flush();
        return new JsonResponse(array('status' => 'OK'));
    }
}
