<?php
namespace AdminBundle\Services;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class UserStats extends BaseBlockService {

    private $doctrine;

    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'url'      => false,
            'title'    => 'Estadisticas Registro Usuarios',
            'template' => 'AdminBundle:Block:userStats.html.twig',
        ));
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
    
        $settings = $blockContext->getSettings();
        $query = $this->doctrine->getManager()->createQuery('SELECT count(u.id), DATE(u.created) as date_created from AppBundle:User u group by date_created order by u.created DESC');
        $registers = $query->setMaxResults(30)->getResult();
        
        $query2 = $this->doctrine->getManager()->createQuery('SELECT count(r.id), DATE(r.created) as date_created from AppBundle:Review r group by date_created order by r.created DESC');
        $reviews = $query2->setMaxResults(30)->getResult();

        return $this->renderResponse($blockContext->getTemplate(), array(
           'registers' => $registers,
           'reviews'   => $reviews,
           'block'     => $blockContext->getBlock(),
           'settings'  => $settings
        ), $response);
        
    }
}