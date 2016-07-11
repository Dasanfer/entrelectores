<?php

namespace AppBundle\Controller\PublicSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class PollController extends Controller
{
    /**
     * @Route("/poll_esi", name="poll_sidebar")
     */
    public function pollSidebarAction()
    {
        $poll = $this->get('app.poll')->getLastPoll();
        $trivia = $this->get('app.poll')->getLastTrivia();
        $previousTrivia = $this->get('app.poll')->getPreviousTrivia();

        $response = $this->render('AppBundle:poll:polls_sidebar.html.twig',array('poll' => $poll,'trivia' => $trivia,'previousTrivia' => $previousTrivia));

        return $response;
    }

    /**
     * @Route("/poll_data/{slug}", name="poll_data")
     */
    public function pollSidebarData($slug)
    {
        $poll = $this->get('doctrine')->getRepository('AppBundle:Poll')->findOneBySlug($slug);

        if(is_null($poll))
            throw $this->createNotFoundException();

        $response = $this->render('AppBundle:poll:polls_sidebar_data.html.twig',array('poll' => $poll));

        $response->setPublic();
        $response->setSharedMaxAge(600);
        return $response;
    }

    /**
     * @Route("/poll_vote", name="poll_vote", options={"expose" = true})
     * @Method("POST")
     */
    public function pollVoteAction(Request $request)
    {
        $ip = $request->getClientIp();
        $optionId = $request->request->get('option');
        $poll = $this->get('app.poll')->vote($optionId,$ip);

        if(is_null($poll)){
            throw $this->createNotFoundException();
        }

        $this->get('doctrine')->getManager()->flush();

        return $this->pollSidebarData($poll->getSlug());
    }

    /**
     * @Route("/encuestas/{page}", name="polls", defaults={"page" = 1})
     */
    public function pollListAction($page)
    {

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT b FROM AppBundle\Entity\Poll b ORDER BY b.created DESC";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            10/*limit per page*/
        );

        $response = $this->render('AppBundle:poll:polls.html.twig',array('pagination' => $pagination));
        $response->setPublic();
        $response->setSharedMaxAge(600);
        return $response;
    }

}
