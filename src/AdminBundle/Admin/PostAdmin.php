<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PostAdmin extends Admin
{


    protected $baseRouteName = 'admin_post';
    protected $baseRoutePattern = 'post';

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

            ->add('title',null,array('label'=>'Titulo'))
            ->add('intro',null,array('label'=>'Intro'))
            ->add('content',null,array('label'=>'Contenido'))
            ->add('category',null,array('label'=>'Categoría'));
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('imageDir',null, array('label' => 'Imágen','template' => 'AdminBundle:Interview:list_image.html.twig'))
            ->add('title',null,array('label'=>'Titulo'))
            ->add('slug',null,array('label'=>'Slug'))
            ->add('created',null,array('label'=>'Fecha Creacion','format'=>'d/m/Y H:i'))
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
            ->add('title',null,array('label'=>'Titulo'))
            ->add('category',null,array('label'=>'Categoría'))
            ->add('intro',null,array('label'=>'Intro'));

        if ($this->id($this->getSubject())) {
            $formMapper->add('slug',null, array('label' => 'Slug','required' => true));
        }
        $formMapper->add('file', 'file', array('label' => 'Imágen Post','required' => false));
        $formMapper->add('content','ckeditor', array('label' => 'Contenido', 'config_name' => 'default'));
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id',null,array('label'=>'Id'))
            ->add('title',null,array('label'=>'Titulo'))
            ->add('intro',null,array('label'=>'Intro'))
            ->add('slug',null,array('label'=>'Slug'))
            ->add('category',null,array('label'=>'Categoría'))
            ->add('created',null, array('label' => 'Fecha Creación','format'=>'d/m/Y H:i'))
        ;
    }


    public function setBaseRouteName($baseRouteName)
    {
        $this->baseRouteName = $baseRouteName;
    }
}
