<?php

namespace AppBundle\Controller\PrivateSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Message;
use AppBundle\Forms\MessageType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class MessageController extends Controller
{

    /**
     * @Route("/post_message", name="post_message", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     * @Method("POST")
     */
    public function postMessageAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');
        $message = new Message();
        $message->setFrom($this->getUser());

        $form = $this->createForm(new MessageType() ,$message, ['em' => $this->get('doctrine')->getManager()]);
        $form->handleRequest($request);

        if($form->isValid()){
            if($this->get('app.message')->checkMessageRights($this->getUser(),$message->getTo())) {
                $this->get('doctrine')->getManager()->persist($message);
                $this->get('doctrine')->getManager()->flush();
                $response =  new JsonResponse(array('id' => $message->getId()),201);
                $response->headers->set('Content-Type', 'application/json');
            } else {
                return new Response('Not follower',403);
            }

        } else {
            $formData = $serializer->serialize($form->getErrors(), 'json');
            $response =  new Response($formData,400);
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }

    /**
     * @Route("/mensajes", name="conversation_index", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     */
    public function messageIndexAction(Request $request)
    {
        $unreadedMessages = $this->get('app.message')->getUserUnreadedMessages($this->getUser());
        $readedMessages = $this->get('app.message')->getUserReadedMessages($this->getUser());

        $response = $this->render('AppBundle:message:index.html.twig', array('unreadedMessages' => $unreadedMessages, 'readedMessages' => $readedMessages));

        return $response;
    }

    /**
     * @Route("/mensajes/{user_id}", name="conversation_with_user", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     */
    public function messageConversationAction($user_id, Request $request)
    {
        $user = $this->get('doctrine')->getRepository('AppBundle:User')->findOneBy(array('id' => $user_id));

        if(is_null($user)){
            throw $this->createNotFoundException();
        }

        $messages = $this->get('app.message')->getMessagesBetweenAndSetReaded($this->getUser(), $user, 0, 10);
        $canMessage = $this->get('app.message')->checkMessageRights($user,$this->getUser());
        $response = $this->render('AppBundle:message:conversation.html.twig',
            array(
                'user' => $user,
                'messages' => $messages,
                'canMessage' => $canMessage
            )
        );
        return $response;
    }

    /**
     * @Route("/user_messages/{user_id}/{offset}", name="user_messages",options={"expose"=true} , defaults={"offset"=0} )
     * @Security("has_role('ROLE_USER')")
     */
    public function userMessagesAction($user_id, $offset){

        $element = null;
        $quantity = 10;

        $user = $this->get('doctrine')->getRepository('AppBundle:User')->findOneBy(array('id' => $user_id));

        if(is_null($user)){
            throw $this->createNotFoundException();
        }

        $messages = $this->get('app.message')->getMessagesBetweenAndSetReaded($this->getUser(), $user, $offset, $quantity);

        $response = $this->render('AppBundle:message:conversationlist.html.twig', array('messages' => $messages,'user'  => $user));

        return $response;
    }

    /**
     * @Route("/my_mutual_followers", name="my_mutual_followers")
     * @Security("has_role('ROLE_USER')")
     */
    public function myMutualFollowersAction(){
        $user = $this->getUser();
        $users = $this->get('app.message')->getMutualFollowers($user);
        $response = $this->render('AppBundle:message:mutual_followers.html.twig', array('users' => $users ));
        return $response;
    }

}
