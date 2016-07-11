<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class AuthorAdmin extends Admin
{
    protected $baseRouteName = 'admin_author';
    protected $baseRoutePattern = 'author';

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
            ->add('name',null, array('label' => 'Nombre'))
            ->add('slug',null, array('label' => 'Slug'))
            ->add('popular',null, array('label' => 'Popular'))
            ->add('info',null, array('label' => 'Información'))
        ;
   }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('imageDir',null, array('label' => 'Imágen','template' => 'AdminBundle:Author:list_image.html.twig'))
            ->add('name',null, array('label' => 'Nombre'))
            ->add('slug',null, array('label' => 'Slug'))
            ->add('popular',null, array('label' => 'Popular'))
            ->add('info',null, array('label' => 'Información'))
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
            ->add('name',null, array('label' => 'Nombre')) ;

        if ($this->id($this->getSubject())) {
            $formMapper->add('slug',null, array('label' => 'Slug','required' => true));
        }

        $formMapper
            ->add('popular',null, array('label' => 'Popular'))
            ->add('info',null, array('label' => 'Información'))
            ->add('file', 'file', array('label' => 'Imágen Autor','required' => false))
            ->add('richInfo','ckeditor', array('label' => 'Infomación Rica', 'config_name' => 'default'))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name',null, array('label' => 'Nombre'))
            ->add('slug',null, array('label' => 'Slug'))
            ->add('popular',null, array('label' => 'Popular'))
            ->add('info',null, array('label' => 'Información'))
        ;
    }

     public function setBaseRouteName($baseRouteName)
    {
        $this->baseRouteName = $baseRouteName;
    }
}
