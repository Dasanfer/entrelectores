<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use AppBundle\Entity\User;

class UserAdmin extends Admin
{
    private $userManager;

    protected $baseRouteName = 'admin_user';
    protected $baseRoutePattern = 'user';

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
            ->add('username',null, array('label' => 'Nombre de Usuario'))
            ->add('email',null, array('label' => 'Email'))
            ->add('globalFollow',null, array('label' => 'Seguimiento global'))
            ->add('enabled',null, array('label' => 'Activado'))

        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('username',null, array('label' => 'Nombre de Usuario'))
            ->add('email',null, array('label' => 'Email'))
            ->add('enabled',null, array('label' => 'Activado'))
            ->add('lastLogin',null, array('label' => 'Ultima Conexión','format'=>'d/m/Y H:i'))
            ->add('locked',null, array('label' => 'Bloqueado', 'required' => false))
            ->add('created',null, array('label' => 'F. Creación','format'=>'d/m/Y H:i'))
            ->add('name',null, array('label' => 'Nombre'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                    'login' => array('template' => 'AdminBundle:User:list__action_login.html.twig')
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
            ->add('username',null, array('label' => 'Usuario'))
            ->add('email',null, array('label' => 'Email'))
            ->add('enabled',null, array('label' => 'Activado', 'required' => false))
            ->add('globalFollow',null, array('label' => 'Seguimiento global'))
            ->add('publicProfile',null, array('label' => 'Perfil público'))
            ->add('name',null, array('label' => 'Nombre'));

        if ($this->id($this->getSubject())) {
            $formMapper
                ->add('gender','choice', array('choices' =>
                                                array(User::FEMALE => 'Mujer', User::MALE => 'Hombre'),
                                                'label'=>'Sexo')
                        )
                ->add('country',null,array('label' => 'País'))
                ->add('city',null,array('label' => 'Ciudad'))
                ->add('cita',null, array('label' => 'Cita'))
                ->add('birthday',null,array('label' => 'Cumpleaños'));
        }else{
             $formMapper->add('password',null, array('label' => 'Contraseña'));
        }


        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('username',null, array('label' => 'Nombre de Usuario'))
            ->add('email',null, array('label' => 'Email'))
            ->add('emailCanonical',null, array('label' => 'Email Canónico'))
            ->add('globalFollow',null, array('label' => 'Seguimiento global'))
            ->add('publicProfile',null, array('label' => 'Perfil público'))
            ->add('enabled',null, array('label' => 'Activado'))
            ->add('locked',null, array('label' => 'Bloqueado'))
            ->add('passwordRequestedAt',null, array('label' => 'Ultima password solicitada'))
            ->add('roles',null, array('label' => 'ROLES'))
            ->add('name',null, array('label' => 'Nombre'))
            ->add('gender',null, array('label' => 'Sexo'))
            ->add('country',null, array('label' => 'Pais'))
            ->add('city',null, array('label' => 'Ciudad'))
            ->add('cita',null, array('label' => 'Cita'))
            ->add('birthday',null, array('label' => 'Fecha de Nacimiento','format'=>'d/m/Y H:i'))
            ->add('created',null, array('label' => 'Fecha de Creación','format'=>'d/m/Y H:i'))
            ->add('updated',null, array('label' => 'Última Actualización','format'=>'d/m/Y H:i'))
        ;
    }

    protected function configureRoutes(RouteCollection $collection) {
        $collection
            ->add('login', $this->getRouterIdParameter() . '/login');
    }

    public function prePersist($user){

        $user->setPlainPassword($user->getPassword());
        $this->getUserManager()->updatePassword($user);
    }

    public function setBaseRouteName($baseRouteName)
    {
        $this->baseRouteName = $baseRouteName;
    }

    public function setUserManager($userManager)
    {
        $this->userManager = $userManager;
    }

    public function getUserManager()
    {
        return $this->userManager;
    }

}
