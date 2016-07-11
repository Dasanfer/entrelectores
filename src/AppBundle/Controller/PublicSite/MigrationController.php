<?php

namespace AppBundle\Controller\PublicSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventDispatcher,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken,
    Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use AppBundle\Entity\User;

class MigrationController extends Controller
{

    private $templating;
    private $doctrine;
    private $router;

    public function __construct($doctrine = null,$templating = null,$router = null)
    {
        $this->templating = $templating;
        $this->doctrine = $doctrine;
        $this->router = $router;
    }

    /**
     * @Route("/libro/{oldId}.{oldSlug}", name="old_book_no_slash")
     */
    public function oldBookAction($oldId,$oldSlug)
    {
        $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('oldId' => $oldId));
        if(is_null($book))
            $book = $this->get('doctrine')->getRepository('AppBundle:Book')->findOneBy(array('oldSlug' => $oldSlug));

        if(is_null($book))
            return $response = new Response($this->get('templating')->render('TwigBundle:Exception:error404.html.twig'),410);

        return $this->redirectToRoute(
            'bookpage',
            array('slug' => $book->getSlug(), 'authorslug' => $book->getAuthor()->getSlug()),
            301
        );
    }

    /**
     * @Route("/autor_libro/{oldSlug}", name="old_author_libro_no_slash")
     * @Route("/autores/{oldSlug}", name="old_author")
     */
    public function oldAuthorAction($oldSlug)
    {
        $author = $this->get('doctrine')->getRepository('AppBundle:Author')->findOneBy(array('oldSlug' => $oldSlug));

        if(is_null($author)){
            return $response = new Response($this->get('templating')->render('TwigBundle:Exception:error404.html.twig'),410);
        }

        return $this->redirectToRoute(
            'authorpage',
            array('slug' => $author->getSlug()),
            301
        );
    }

    /**
     * @Route("/autores/index/{oldSlug}", name="old_author_index")
     */
    public function oldAuthorIndexAction($oldSlug)
    {
        $author = $this->get('doctrine')->getRepository('AppBundle:Author')->findOneBy(array('name' => $oldSlug));

        if(is_null($author)){
            return $response = new Response($this->get('templating')->render('TwigBundle:Exception:error404.html.twig'),410);
        }

        return $this->redirectToRoute(
            'authorpage',
            array('slug' => $author->getSlug()),
            301
        );
    }


    /**
     * @Route("/tag/{oldtag}", name="old_tag")
     */
    public function oldTagAction($oldtag)
    {
        return $this->redirectToRoute(
            'search_books',
            array('term' => $oldtag),
            301
        );
    }

    /**
     * @Route("/grupos", name="old_groups_slash")
     * @Route("/comunidad", name="old_community_slash")
     */
    public function oldGroupsAction()
    {
        return $this->redirectToRoute(
            'public_lists',
            array(),
            301
        );
    }

    /**
     * @Route("/actividad", name="old_activity")
     */
    public function oldActivityAction()
    {
        return $this->redirectToRoute(
            'public_blog_post_list',
            array(),
            301
        );
    }

    /**
     * @Route("/actividad/{one}", name="old_activity_one", defaults={"two" = 1})
     * @Route("/actividad/{one}/{two}", name="old_activity_two")
     */
    public function oldActivityOneAction($one,$two)
    {
        return $response = new Response($this->get('templating')->render('TwigBundle:Exception:error404.html.twig'),410);
    }

    /**
     * @Route("/comunidad/{slug}", name="old_user_community")
     * @Route("/comunidad/{slug}/{more}", name="old_user_community_more", requirements={"more":".*"})
     */
    public function oldUserCommunityAction($slug)
    {
        $user = $this->get('doctrine')->getRepository('AppBundle:User')->findOneBySlug($slug);
        if(!is_null($user)){
            $response = $this->redirectToRoute('user_profile',array('slug' => $slug),301);
        } else {
            $response = new Response($this->get('templating')->render('TwigBundle:Exception:error404.html.twig'),410);
        }
        return $response;
    }

    /**
     * @Route("/lectores/perfil/{username}", name="old_username_profile")
     */
    public function oldUserNameProfileAction($username)
    {
        $user = $this->get('doctrine')->getRepository('AppBundle:User')->findOneByUsername($username);
        if(!is_null($user)){
            $response = $this->redirectToRoute('user_profile',array('slug' => $user->getSlug()),301);
        } else {
            $response = new Response($this->get('templating')->render('TwigBundle:Exception:error404.html.twig'),410);
        }
        return $response;
    }


    /**
     * @Route("/resena/{slug}", name="old_resena_slug")
     */
    public function oldResenaAction()
    {
        return new Response($this->get('templating')->render('TwigBundle:Exception:error404.html.twig'),410);
    }

    /**
     * @Route("/librerias/{slug}", name="old_library_slug")
     * @Route("/librerias", name="old_library")
     */
    public function oldLibrariesAction()
    {
        return new Response('',410);
    }

    /**
     * @Route("/libros/ediciones/{slug}", name="old_ediciones_book")
     */
    public function oldEdicionesAction($slug)
    {
        $repo = $this->doctrine->getRepository('AppBundle:Book');
        $book = $repo->findOneByOldSlug($slug);
        if(is_null($book)){
            $book = $repo->findOneBySlug($slug);
        }

        if(!is_null($book)){
            $newRoute = $this->router->generate('bookpage',array('slug' => $book->getSlug(), 'authorslug' => $book->getAuthor()->getSlug()));
            $response = new RedirectResponse($newRoute,301);
        } else {
            $response = new Response($this->templating->render('TwigBundle:Exception:error404.html.twig'),410);
        }

        return $response;
    }

    /**
     * @Route("/libros/detalle/{slug}", name="old_detail_book")
     */
    public function oldBookDetailAction($slug)
    {
        $repo = $this->doctrine->getRepository('AppBundle:Book');
        $pattern = '/(?P<oldid>\d*)\.?(?P<slug>.*)/';
        $book = null;
        preg_match($pattern, $slug, $matches);

        if(array_key_exists('oldid',$matches) && count($matches['oldid']) > 0 ){
            $book = $repo->findOneByOldId($matches['oldid']);
        }

        if(is_null($book) && array_key_exists('slug',$matches) && count($matches['slug']) > 0){
            $book = $repo->findOneByOldSlug($matches['slug']);
        }

        if(!is_null($book)){
            $newRoute = $this->router->generate('bookpage',array('slug' => $book->getSlug(), 'authorslug' => $book->getAuthor()->getSlug()));
            $response = new RedirectResponse($newRoute,301);
        } else {
            $response = new Response($this->templating->render('TwigBundle:Exception:error404.html.twig'),410);
        }

        return $response;
    }
}
