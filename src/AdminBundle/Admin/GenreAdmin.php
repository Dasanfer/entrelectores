<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class GenreAdmin extends Admin
{
    protected $baseRouteName = 'admin_genre';
    protected $baseRoutePattern = 'genre';

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
            ->add('featured',null, array('required' => false, 'label' => 'Descatado'))
            ->add('info',null, array('label' => 'Información'))
            ->add('description',null, array('label' => 'Descripción'))
            ->add('slug',null, array('label' => 'Slug'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name',null, array('label' => 'Nombre'))
            ->add('imageDir',null, array('label' => 'Portada','template' => 'AdminBundle:Book:book_image_featured.html.twig'))
            ->add('featured',null, array('required' => false, 'label' => 'Descatado'))
            ->add('info',null, array('label' => 'Información'))
            ->add('description',null, array('label' => 'Descripción'))
            ->add('slug',null, array('label' => 'Slug'))
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
            ->add('name',null, array('label' => 'Nombre'))
            ->add('featured',null, array('required' => false, 'label' => 'Descatado'))
            ->add('info','textarea', array('label' => 'Información', 'max_length' => 150))
            ->add('description','textarea', array('label' => 'Descripción', 'max_length' => 250));
            
        if ($this->id($this->getSubject())) {
            $formMapper->add('slug',null, array('label' => 'Slug','required' => true));
        }
        
        $formMapper->add('file', 'file', array('label' => 'Imágen Post','required' => false));
        
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name',null, array('label' => 'Nombre'))
            ->add('featured',null, array('required' => false, 'label' => 'Descatado'))
            ->add('info',null, array('label' => 'Información'))
            ->add('description',null, array('label' => 'Descripción'))
            ->add('slug',null, array('label' => 'Slug'))
        ;
    }

    public function setBaseRouteName($baseRouteName)
    {
        $this->baseRouteName = $baseRouteName;
    }
}
