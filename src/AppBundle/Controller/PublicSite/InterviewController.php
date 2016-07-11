<?php

namespace AppBundle\Controller\PublicSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class InterviewController extends Controller
{
    /**
     * @Route("/entrevistas/{slug}", name="public_interview")
     */
    public function publicInterviewAction($slug)
    {

        $interview = $this->get('doctrine')->getRepository('AppBundle:Interview')->findOneBy(array('slug' => $slug));

        $response = $this->render('AppBundle:public:interview.html.twig',array('interview' => $interview));

        $response->setSharedMaxAge(600);

        return $response;
    }

    /**
     * @Route("/blog/{slug}", name="public_blog_post")
     */
    public function publicBlogPostAction($slug)
    {

        $post = $this->get('doctrine')->getRepository('AppBundle:Post')->findOneBy(array('slug' => $slug));
        if(is_null($post))
            throw $this->createNotFoundException();

        $response = $this->render('AppBundle:public:blog_post.html.twig',array('post' => $post));

        $response->setSharedMaxAge(600);

        return $response;
    }

    /**
     * @Route("/blog/category/{slug}/{page}", name="category_blog_post_paged")
     * @Route("/blog/category/{slug}", name="category_blog_post", defaults={"page" = 1})
     */
    public function publicBlogCategoryAction($slug, $page)
    {    
        $em    = $this->get('doctrine.orm.entity_manager');
        
        if($slug != 'SinCategoria'){
            $cate = $this->get('doctrine')->getRepository('AppBundle:PostCategory')->findOneBy(array('slug' => $slug));
            $id = $cate->getId();
            $dql   = "SELECT b FROM AppBundle:Post b WHERE b.category = " . $id . " order by b.created DESC";
        }else{
            $dql   = "SELECT b FROM AppBundle:Post b WHERE b.category IS NULL order by b.created DESC";
        }
        
        $query = $em->createQuery($dql);
        
        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            5/*limit per page*/
        );

        $pagination->setUsedRoute('category_blog_post_paged');
        $pagination->setTemplate('AppBundle:public:pagination.html.twig');

        $response = $this->render('AppBundle:public:blog_categories.html.twig',array('pagination' => $pagination, 'category' => $slug));

        $response->setSharedMaxAge(3600);
        
        return $response;
    }

    /**
     * @Route("/timeline_interview/{offset}", name="timeline_interview", options={"expose":true})
     */
    public function timelineInterviewAction($offset)
    {

        $interviews = $this->get('doctrine')->getRepository('AppBundle:Interview')->findBy(array(),array('created' => 'DESC'),1,$offset);

        if(count($interviews) > 0)
            $response = $this->render('AppBundle:logged:timeline/interview_entry.html.twig',array('interview' => $interviews[0]));
        else
            $response = new Response();

        $response->setSharedMaxAge(600);

        return $response;
    }

    /**
     * @Route("/entrevistas/page/{page}", name="public_interview_list_paged")
     * @Route("/entrevistas", name="public_interview_list", defaults={"page" = 1})
     */
    public function interviewListAction($page)
    {

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT b FROM AppBundle:Interview b order by b.created DESC";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            5/*limit per page*/
        );

        $pagination->setUsedRoute('public_interview_list_paged');
        $pagination->setTemplate('AppBundle:public:pagination.html.twig');

        $response = $this->render('AppBundle:public:interview_list.html.twig',array('pagination' => $pagination));

        $response->setSharedMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/blog/page/{page}", name="public_blog_post_list_paged")
     * @Route("/blog", name="public_blog_post_list", defaults={"page" = 1})
     */     
    public function blogPostListAction($page)
    {

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT b FROM AppBundle:Post b order by b.created DESC";
        $query = $em->createQuery($dql);
        
        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            5/*limit per page*/
        );

        $pagination->setUsedRoute('public_blog_post_list_paged');
        $pagination->setTemplate('AppBundle:public:pagination.html.twig');

        $response = $this->render('AppBundle:public:blog_post_list.html.twig',array('pagination' => $pagination));

        $response->setSharedMaxAge(3600);
        return $response;
    }
}
