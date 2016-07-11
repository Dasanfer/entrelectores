<?php
namespace AppBundle\Controller\PublicSite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GenreController extends Controller
{

    /**
     * @Route("/generos", name="all_genre_page")
     * @Route("/generos/", name="all_genre_page_slash")
     */
    public function allGenresAction()
    {
        $featus = $this->get('doctrine')->getRepository('AppBundle:Genre')->findByFeatured(true);
        $genres = $this->get('doctrine')->getRepository('AppBundle:Genre')->findAll();
        
        $data = array();
        
        $em = $this->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        
        $qb->select('b')->from('AppBundle:Book','b')
           ->where($qb->expr()->eq('b.genre',':genre'))
           ->andWhere($qb->expr()->eq('b.featured',':featured'))
           ->setMaxResults(5);

        foreach($featus as $genre){
           $qb->setParameters(array('genre' => $genre, 'featured' => true));
           $data[] = array('genre' => $genre, 'books' => $qb->getQuery()->getResult());
        }
        
        $response = $this->render('AppBundle:public:all_genres.html.twig',array('genres' => $genres, 'data' => $data));
        
        return $response;
    }

   /**
     * @Route("/generos/{slug}/page/{page}", name="bookgenrepaged")
     * @Route("/generos/{slug}/page/{page}/", name="bookgenrepaged_slash")
     * @Route("/generos/{slug}", name="bookgenre", defaults={"page" = 1})
     * @Route("/generos/{slug}/", name="bookgenre_slash", defaults={"page" = 1})
     */
    public function genreAction($slug,$page)
    {
        $request = $this->getRequest();
        $genre = $this->get('doctrine')->getRepository('AppBundle:Genre')->findOneBy(array('slug' => $slug));

        if(is_null($genre))
            throw $this->createNotFoundException('No se ha encontrado el género');

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT b FROM AppBundle:Book b where b.genre = :genre order by b.ratingCount  DESC";
        $query = $em->createQuery($dql);

        $query->setParameter('genre',$genre);

        $dql2 = "SELECT b FROM AppBundle:Book b where b.genre = :genre";

        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            10/*limit per page*/
        );
        
        $pagination->setUsedRoute('bookgenrepaged');
        $pagination->setTemplate('AppBundle:public:pagination.html.twig');
        $response = $this->render('AppBundle:book:genre_page.html.twig',array('genre' => $genre,'pagination' => $pagination));

        $response->setPublic();
        $response->setSharedMaxAge(3600);
        return $response;
    }
}
