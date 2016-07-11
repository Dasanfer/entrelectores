<?php

namespace AppBundle\Controller\PrivateSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Comment;
use AppBundle\Forms\CommentType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\FollowEvent;
use AppBundle\Services\Timeline\TimelineEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommentController extends Controller
{
    /**
     * @Route("/post_comment", name="post_comment", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     * @Method("POST")
     */
    public function postCommentAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');
        $comment = new Comment();
        $comment->setCreatedBy($this->getUser());

        $form = $this->createForm(new CommentType(),$comment,['em' => $this->get('doctrine')->getManager()]);
        $form->handleRequest($request);
        if($form->isValid()){

            $event = new TimelineEvent($this->getUser());
            $event->setComment($comment);

            if(!is_null($comment->getBook())){
                $event->setBook($comment->getBook());
                $event->setType(FollowEvent::comment);
            }

            if(!is_null($comment->getList())){
                $event->setList($comment->getList());
                $event->setType(FollowEvent::list_comment);
            }

            if(!is_null($comment->getAuthor())){
                $event->setAuthor($comment->getAuthor());
                $event->setType(FollowEvent::author_comment);
            }

            $this->get('event_dispatcher')->dispatch('app.timeline_event',$event);

            $this->get('doctrine')->getManager()->persist($comment);
            $this->get('doctrine')->getManager()->flush();
            $response =  new JsonResponse(array('id' => $comment->getId()),201);
            $response->headers->set('Content-Type', 'application/json');
        } else {
            $formData = $serializer->serialize($form->getErrors(), 'json');
            $response =  new Response($formData,400);
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }

    /**
     * @Route("/conversation/{id}/children/{offset}", name="comments_children", defaults={"offset"=0}, options={"expose"=true})
     */
    public function commentChildrenAction($id,$offset, Request $request)
    {
        $comment = $this->get('doctrine')->getRepository('AppBundle:Comment')->findOneBy(array('id' => $id));

        if(is_null($comment))
            throw $this->createNotFoundException('No se ha encontrado el comentario');

        $comments = $this->get('app.conversation')->getConversationChildren($comment,$offset,20);

        $serializer = $this->get('jms_serializer');
        $response =  new Response($serializer->serialize($comments,'json'));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('total',$comment->getChildren()->count());

        return $response;
    }

    /**
     * @Route("/element_conversation/{type}/{id}/{offset}", name="element_conversation", defaults={"offset"=0}, options={"expose"=true})
     */
    public function elementConversation($type,$id,$offset){
        $element = null;
        $quantity = 10;
        if($type == 'book'){
            $element = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('id' => $id));
        }
        else if($type == 'author'){
            $element = $this->get('doctrine')->getRepository('AppBundle:Author')->findOneBy(array('id' => $id));
        }
        else if($type == 'list'){
            $element = $this->get('doctrine')->getRepository('AppBundle:BookList')->findOneBy(array('id' => $id));
        }

        if(is_null($element))
            throw $this->createNotFoundException('No se ha encontrado el elemento');

        if($type == 'book'){
            $comments = $this->get('app.conversation')->getBookConversations($element,$offset,$quantity);
        }
        else if($type == 'author'){
            $comments = $this->get('app.conversation')->getAuthorConversations($element,$offset,$quantity);
        }
        else if($type == 'list'){
            $comments = $this->get('app.conversation')->getListConversations($element,$offset,$quantity);
        }

        $response  = $this->render('AppBundle:public:element_comments.html.twig',array('comments' => $comments,'type' => $type, 'element' => $element));

        $response->headers->set('total',$element->getComments()->count());
        return $response;
    }
}
