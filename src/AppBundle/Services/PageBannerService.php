<?php
namespace AppBundle\Services;
use AppBundle\Entity\PageBanner;

class PageBannerService extends \Twig_Extension
{
    private $doctrine;

    public function __construct($doctrine){
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            'getPageBanners' => new \Twig_Function_Method($this, 'getPageBanners')
        );
    }


    public function getPageBanners(){

        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select('b')->from('AppBundle:PageBanner','b')
            ->where($qb->expr()->eq('b.active',':active'))
            ->orderBy('b.updated','DESC')->setMaxResults(1);

        $qb->setParameters(array(
            'active' => true
        ));

        return $qb->getQuery()->getResult();
    }

    public function getName()
    {
        return 'app_page_banner';
    }

}
