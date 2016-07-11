<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ReviewAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,           
        '_sort_order' => 'DESC', 
        '_sort_by' => 'created' 
    );
        
    protected $baseRouteName = 'admin_review';
    protected $baseRoutePattern = 'review';
    
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('book.title',null, array('label' => 'Libro'))
            ->add('user.username',null, array('label' => 'Usuario'))
            ->add('title',null, array('label' => 'Titulo Review'))
            ->add('text',null, array('label' => 'Review'))
            ->add('flag',null, array('label' => 'Flag'))
            ->add('created',null, array('label' => 'F.Creación'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('created',null, array('label' => 'F.Creación','format'=>'d/m/Y H:i'))
            ->add('book.title',null, array('label' => 'Libro'))
            ->add('title',null, array('label' => 'Titulo Review'))
            ->add('text',null, array('label' => 'Texto'))
            ->add('flag',null, array('label' => 'Flag')) 
            ->add('user.username',null, array('label' => 'Usuario'))
        
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('created',null, array('label' => 'F.Creación','format'=>'d/m/Y H:i'))
            ->add('book',null, array('label' => 'Libro'))
            ->add('title',null, array('label' => 'Titulo Review'))
            ->add('text',null, array('label' => 'Texto'))
            ->add('flag',null, array('label' => 'Flag')) 
            ->add('user',null, array('label' => 'Usuario'))
        ;
    }
    
    protected function configureRoutes(RouteCollection $collection) {
        $collection
            ->remove('create')
            ->remove('edit');
    }
    
    public function setBaseRouteName($baseRouteName)
    {
        $this->baseRouteName = $baseRouteName;
    }
}
