<?php

namespace AppBundle\Controller\PublicSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken,
    Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use AppBundle\Entity\User;

class HomeController extends Controller
{


    /**
     *
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request){
        $user = new User();
        $registerForm = $this->createForm('small_user_registration',$user);

        if($request->getMethod() == 'POST'){
            $registerForm->handleRequest($request);
            if($registerForm->isValid()){
                $user->setEnabled(true);
                $this->get('fos_user.user_manager')->updateUser($user);
                //LOG USER
                $token = new UsernamePasswordToken($user, $user->getPassword(), "public", $user->getRoles());
                $this->get("security.context")->setToken($token);
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
                //end login user
                return $this->redirect($this->generateUrl('user_homepage'), 301);
            }
        }

        $ip = $request->getClientIp();

        if($ip == "::1"){
            $ip = "193.0.0.2";
        }

        $geoip = $this->get('maxmind.geoip')->lookup($ip);

        if($geoip !== false)
            $country = $geoip->getCountryName();
        else
            $country = "";

        return $this->render('AppBundle:public:index.html.twig',
            array(
                'form'    => $registerForm->createView(),
                'country' => $country
            )
        );
    }

    /**
     * @Route("/index_body", name="index_body")
     */
    public function indexBodyAction(Request $request)
    {
        $appDomain = $this->container->getParameter('app_domain');
        $fbAppId = $this->container->getParameter('facebook_app_id');
        $genres = $this->get('doctrine')->getRepository('AppBundle:Genre')->findAll();


        $promoted = $this->get('app.promoted')->getPromoted(2);

        $qb = $this->get('doctrine')->getManager()->createQueryBuilder();
        $qb->select('b')
            ->from('AppBundle:Book','b')
            ->where($qb->expr()->isNotNull('b.imageDir'))
            ->orderBy('b.updated','DESC');
        $query = $qb->getQuery();
        $books = $query->setMaxResults(2)->setFirstResult(rand(0,300))->getResult();

        $response = $this->render('AppBundle:public:index_body.html.twig',
            array(
                'genres' => $genres,
                'app_domain' => $appDomain,
                'fb_app_id' => $fbAppId,
                'promoted' => $promoted,
                'books' => $books
            )
        );

        $response->setPublic();
        $response->setSharedMaxAge(3600);
        return $response;
    }


    /**
     * @Route("/common_header", name="common_header")
     */
    public function commonHeaderAction(Request $request)
    {
        $ip = $request->getClientIp();

        if($ip == "::1"){
            $ip = "193.0.0.2";
        }

        $geoip = $this->get('maxmind.geoip')->lookup($ip);

        if($geoip !== false)
            $country = $geoip->getCountryName();
        else
            $country = "";

        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            $response = $this->render('AppBundle:logged:logged_header.html.twig',array('country' => $country));
        else {
            $response = $this->render('AppBundle:public:no_logged_header.html.twig',array('country' => $country));
        }
        return $response;
    }

    /**
     * @Route("/home_footer", name="homefooter")
     */
    public function homeFooterAction()
    {
        $appDomain = $this->container->getParameter('app_domain');
        $fbAppId = $this->container->getParameter('facebook_app_id');
        $user = new User();
        $registerForm = $this->createForm('small_user_registration',$user);
        $response = $this->render('AppBundle:public:footer.html.twig',array('app_domain' => $appDomain, 'fb_app_id' => $fbAppId,'registerModalForm' => $registerForm->createView()));
        $response->setPublic();
        $response->setSharedMaxAge(600);
        return $response;
    }

    /**
     * @Route("/home_blog_posts", name="home_blog_posts")
     */
    public function homeBlogPostsAction()
    {
        $interviews = $this->get('doctrine')->getRepository('AppBundle:Interview')->findBy(array(),array('created' => 'DESC'),1);
        if(count($interviews) > 0)
            $interview = $interviews[0];
        else
            $interview = null;

        $posts = $this->get('doctrine')->getRepository('AppBundle:Post')->findBy(array(),array('created' => 'DESC'),3);

        $response = $this->render('AppBundle:public:home_blog_posts.html.twig',array('posts' => $posts,'interview' => $interview));

        $response->setPublic();
        $response->setSharedMaxAge(3600);
        return $response;
    }
}
