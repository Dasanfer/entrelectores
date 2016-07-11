<?php
namespace AppBundle\Services\Books;

class PromotedService
{
    private $doctrine;

    public function __construct($doctrine){
        $this->doctrine = $doctrine;
    }

    public function getPromoted(){
        $repo = $this->doctrine->getRepository('AppBundle:Book');
        $promoted = $repo->findBy(array('promoted' => true));
        return $promoted;
    }

    public function getNovelty(){
        $repo = $this->doctrine->getRepository('AppBundle:Book');
        $novelty = $repo->findBy(array('novelty' => true));
        return $novelty;
    }

}
