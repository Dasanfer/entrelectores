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

/**
 * @Route("/banner")
 */
class BannerController extends Controller
{
    /**
     * @Route("/sidebar", name="sidebar_banner")
     */
    public function sidebarAction()
    {
        $banners = $this->get('doctrine')->getRepository('AppBundle:Banner')->findByActive(true);
        $response = $this->render('AppBundle:banner:sidebar.html.twig',array('banners' => $banners));

        $response->setPublic();
        $response->setSharedMaxAge(600);
        return $response;
    }
}
