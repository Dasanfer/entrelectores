<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PageBannerAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('imgBigDir')
            ->add('imgSmallDir')
            ->add('targetUrl')
            ->add('active')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('imgBigDir',null, array('label' => 'Imagen Grande','template' => 'AdminBundle::list_image.html.twig'))
            ->add('imgSmallDir',null, array('label' => 'Imagen Pequeña','template' => 'AdminBundle::list_image.html.twig'))
            ->add('targetUrl')
            ->add('active')
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
            ->add('name')
            ->add('file', 'file', array('label' => 'Imagen grande','required' => false))
            ->add('file2', 'file', array('label' => 'Imagen pequeña','required' => false))
            ->add('targetUrl')
            ->add('active',null,array('required' => false))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('imgBigDir',null, array('label' => 'Imagen Grande','template' => 'AdminBundle::show_image.html.twig'))
            ->add('imgSmallDir',null, array('label' => 'Imagen Pequeña','template' => 'AdminBundle::show_image.html.twig'))
            ->add('targetUrl')
            ->add('active')
        ;
    }
}
