<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use AppBundle\Entity\BookList;

class BookListAdmin extends Admin
{

    protected $baseRouteName = 'admin_book_list';
    protected $baseRoutePattern = 'book_list';

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
            ->add('name',null, array('label' => 'Nombre de Lista'))
            ->add('text',null, array('label' => 'Texto de la lista'))
            ->add('globalFollow',null, array('label' => 'Seguimiento global'))

                 ->add('publicFlag', 'doctrine_orm_string',array('label'=> "Tipo"),'choice',array(
            'choices' =>array(
                  BookList::USER_PRIVATE => "Privado",
                  BookList::READ_PUBLIC => "Publico (Lectura)",
                  BookList::EDIT_PUBLIC => "Publico (Edicion)",
                )
            ));
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name',null, array('label' => 'Nombre de Lista'))
            ->add('text',null, array('label' => 'Texto de la lista'))
            ->add('globalFollow',null, array('label' => 'Seguimiento global'))
            ->add('publicFlag',null, array('label' => 'Tipo','template'=>'AdminBundle:BookList:list_type.html.twig'))
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
            ->add('name',null, array('label' => 'Nombre de Lista'))
            ->add('text',null, array('label' => 'Texto de la lista'))
            ->add('globalFollow',null, array('label' => 'Seguimiento global'))
            ->add('publicFlag','choice', array('choices'=>array(
                  BookList::USER_PRIVATE => "Privado",
                  BookList::READ_PUBLIC => "Publico (Lectura)",
                  BookList::EDIT_PUBLIC => "Publico (Edicion)",
            ),
             'label' => 'Tipo'))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name',null, array('label' => 'Nombre de Lista'))
            ->add('text',null, array('label' => 'Texto de la lista'))
            ->add('globalFollow',null, array('label' => 'Seguimiento global'))
            ->add('publicFlag',null, array('label' => 'Tipo'))
            ->add('books',null, array('label' => 'Libros'))
        ;
    }

    public function setBaseRouteName($baseRouteName)
    {
        $this->baseRouteName = $baseRouteName;
    }
}
