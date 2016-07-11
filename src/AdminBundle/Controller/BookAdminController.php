<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sonata\AdminBundle\Controller\CRUDController;

class BookAdminController extends CRUDController
{
 
    public function getAuthorsAction(Request $request)
    {   
        $slug=$request->query->get('q');
        
        $query = $this->getDoctrine()
            ->getRepository('AppBundle:Author')
            ->createQueryBuilder('a')
            ->where('a.name LIKE :text')
            ->setParameter('text', '%'.$slug.'%')
            ->getQuery();
           
        
        $authors = $query->getResult();
        $authorsArray=array();
        foreach($authors as $author){
            $authorsArray[$author->getId()]['id']=$author->getId();
            $authorsArray[$author->getId()]['name']=$author->getName();
            $authorsArray[$author->getId()]['slug']=$author->getSlug();
        }

        return new JsonResponse($authorsArray, 200);
    }

}
