<?php
namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder->add('name',null,array('label' => 'Nombre completo:'));
        $builder->add('gender','choice',
            array(
                'label' => 'Sexo:',
                'choices' => array('M' => 'Hombre', 'F' => 'Mujer'),
                'required' => true
            )
        );
        $builder->add('country',null,array('label' => 'País:'));
        $builder->add('city',null,array('label' => 'Ciudad:'));
        $builder->add('birthday','birthday',
            array(
                'label' => 'Fecha de nacimiento:',
                'years' => range(date("Y")-100,date("Y"))
            )
        );
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'app_user_registration';
    }
}
