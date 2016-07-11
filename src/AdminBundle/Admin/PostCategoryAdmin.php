<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PostCategoryAdmin extends Admin
{
    
    protected $baseRouteName = 'admin_post_category';
    protected $baseRoutePattern = 'post_category';
    
    protected $datagridValues = array(
        '_page' => 1,           
        '_sort_order' => 'DESC', 
        '_sort_by' => 'created' 
    );
    

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name',null,array('label'=>'Nombre'))
            ->add('slug',null,array('label'=>'Slug'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name',null,array('label'=>'Nombre'))
            ->add('slug',null,array('label'=>'Slug'))
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
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name',null,array('label'=>'Nombre'))

        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id',null,array('label'=>'Id'))
            ->add('name',null,array('label'=>'Nombre'))
            ->add('slug',null,array('label'=>'Slug'))
        ;
    }
    
    public function setBaseRouteName($baseRouteName)
    {
        $this->baseRouteName = $baseRouteName;
    }
}
