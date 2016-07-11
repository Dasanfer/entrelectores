<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PollAdmin extends Admin
{

    protected $baseRouteName = 'admin_poll';
    protected $baseRoutePattern = 'poll';

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
            ->add('question',null,array('label'=>'Pregunta'))
            ->add('slug',null,array('label'=>'Slug'))
            ->add('active',null,array('label'=>'Activa'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('title',null,array('label'=>'Titulo'))
            ->add('active',null,array('label'=>'Activa'))
            ->add('question',null,array('label'=>'Pregunta'))
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
            ->add('title',null,array('label'=>'Titulo'))
            ->add('active',null,array('label'=>'Activa'))
            ->add('question',null,array('label'=>'Pregunta'))
            ->add('answer',null,array('label'=>'Respuesta'))
            ->add('trivia',null,array('label'=>'Es trivia'))
            ->add('options','sonata_type_collection', array('by_reference' => false), array(
                'edit' => 'inline',
                'inline' => 'table',
                'sortable'  => 'position'
            ))


        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title',null,array('label'=>'Titulo'))
            ->add('active',null,array('label'=>'Activa'))
            ->add('slug',null,array('label'=>'Slug'))
            ->add('question',null,array('label'=>'Pregunta'))
            ->add('created',null,array('label'=>'Fecha Creación','format'=>'d/m/Y H:i'))
            ->add('updated',null,array('label'=>'Fecha Ultima Actualización','format'=>'d/m/Y H:i'))
        ;
    }

    public function setBaseRouteName($baseRouteName)
    {
        $this->baseRouteName = $baseRouteName;
    }
}
