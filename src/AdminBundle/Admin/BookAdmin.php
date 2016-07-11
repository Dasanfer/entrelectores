<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class BookAdmin extends Admin
{
    protected $baseRouteName = 'admin_book';
    protected $baseRoutePattern = 'book';

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

            ->add('title',null, array('label' => 'Titulo'))
            ->add('originalTitle',null, array('label' => 'Título Original'))
            ->add('genre',null, array('label' => 'Género'))
            ->add('featured',null, array('required' => false, 'label' => 'Descatado'))
            ->add('author.name',null, array('label' => 'Autor'))
            ->add('year',null, array('label' => 'Año'))
            ->add('popular',null, array('label' => 'Popular'))
            ->add('novelty',null, array('label' => 'Novedad'))
            ->add('isbn',null, array('label' => 'ISBN'))
            ->add('elcId',null, array('label' => 'Corte Inglés ID'))
            ->add('slug',null, array('label' => 'Slug'))
            ->add('promoted',null, array('label' => 'Promocionado'))

        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('imageDir',null, array('label' => 'Portada','template' => 'AdminBundle:Book:list_cover.html.twig'))
            ->add('title',null, array('label' => 'Titulo'))
            ->add('genre.name',null, array('label' => 'Género'))
            ->add('featured',null, array('required' => false, 'label' => 'Descatado'))
            ->add('author.name',null, array('label' => 'Autor'))
            ->add('slug',null, array('label' => 'Slug'))
            ->add('year',null, array('label' => 'Año'))
            ->add('isbn',null, array('label' => 'ISBN'))
            ->add('promoted',null, array('label' => 'Promocionado'))
            ->add('cachedRate',null, array('label' => 'Calificación'))
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
            ->add('title',null, array('label' => 'Titulo'))
            ->add('originalTitle',null, array('label' => 'Titulo Original'))
            ->add('genre',null, array('label' => 'Género','required' => true))
            ->add('featured',null, array('required' => false, 'label' => 'Descatado'))
            ->add('popular',null, array('label' => 'Popular'))
            ->add('novelty',null, array('label' => 'Novedad'))
            ->add('authorAux','text',array(
                        'attr'=>
                            array('class'=> "author-select",
                                'data-sonata-select2'=>'false'),
                        'label'=> 'Autor'))
            ->add('year',null, array('label' => 'Año','required' => true))
            ->add('elcId',null, array('label' => 'Corte Inglés ID','required'=> false))
            ->add('isbn',null, array('label' => 'ISBN'));

        if ($this->id($this->getSubject())) {
            $formMapper->add('slug',null, array('label' => 'Slug','required' => true));
        }

        $formMapper
            ->add('sinopsis','textarea', array('label' => 'Sinopsis','required' => true))
            ->add('promoted',null, array('label' => 'Promocionado'))
            ->add('file', 'file', array('label' => 'Portada','required' => false))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id',null, array('label' => 'Identificador'))
            ->add('title',null, array('label' => 'Titulo'))
            ->add('originalTitle',null, array('label' => 'Titulo Original'))
            ->add('author',null, array('label' => 'Autor'))
            ->add('genre',null, array('label' => 'Género'))
            ->add('featured',null, array('required' => false, 'label' => 'Descatado'))
            ->add('created',null, array('label' => 'Fecha Creación','format'=>'d/m/Y H:i'))
            ->add('promoted',null, array('label' => 'Promocionado'))
            ->add('popular',null, array('label' => 'Popular'))
            ->add('novelty',null, array('label' => 'Novedad'))
            ->add('year',null, array('label' => 'Año'))
            ->add('isbn',null, array('label' => 'ISBN'))
            ->add('slug',null, array('label' => 'Slug'))
        ;
    }

     public function prePersist($book)
    {
        $this->setAuthorFromAux($book);

    }

    public function preUpdate($book)
    {

        $this->setAuthorFromAux($book);
    }

    private function setAuthorFromAux($book){
        $authorId=$book->getAuthorAux();
        $doctrine=$this->getDoctrine();
        $author=$doctrine->getRepository('AppBundle:Author')->find($authorId);
        $book->setAuthor($author);
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'AdminBundle:Book:edit.html.twig';
                break;
            case 'create':
                return 'AdminBundle:Book:edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

     protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
            $collection->add('getAuthors', 'get-authors');
    }

    public function getDoctrine(){
        $container=$this->getContainer();

        return $container->get('doctrine');
    }

    public function getContainer(){
        return $this->getConfigurationPool()->getContainer();
    }

    public function setBaseRouteName($baseRouteName)
    {
        $this->baseRouteName = $baseRouteName;
    }
}
