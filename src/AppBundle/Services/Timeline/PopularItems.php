<?php
namespace AppBundle\Services\Timeline;
use AppBundle\Entity\BookList;

class PopularItems
{
    private $doctrine;

    public function __construct($doctrine){
        $this->doctrine = $doctrine;
    }

    public function popularLists($count = 3){
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select('l');
        $qb->from('AppBundle\Entity\BookList','l');
        $qb->where($qb->expr()->gte('l.publicFlag',BookList::READ_PUBLIC));
        $qb->orderBy('l.followersCount','DESC');
        $qb->setMaxResults(100);

        $results = $qb->getQuery()->getResult();

        if(count($results) <= $count)
            return $results;
        else {
            shuffle($results);
            return array_slice($results,0,$count);
        }
    }

    public function popularUsers($count = 3){
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select('u');
        $qb->from('AppBundle:User','u');
        $qb->where($qb->expr()->eq('u.publicProfile',true));
        $qb->orderBy('u.followersCount','DESC');
        $qb->setMaxResults(100);

        try {
            $results = $qb->getQuery()->getResult();
        }catch(\Exception $ex){
            $results = array();
        }

        if(count($results) <= $count)
            return $results;
        else {
            shuffle($results);
            return array_slice($results,0,$count);
        }
    }

    public function popularBooks($count = 3){
        $repo = $this->doctrine->getRepository('AppBundle:Book');
        return $repo->findBy(array('popular' => true),null,$count);
    }

    public function popularAuthors($count = 3){
        $repo = $this->doctrine->getRepository('AppBundle:Author');
        return $repo->findBy(array('popular' => true),null,$count);
    }
}
