<?php
namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\True;
use Symfony\Component\Validator\Constraints as Assert;

class SmallRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder->add('username','text',array(
            'label' => 'Usuario',
            'constraints' => array(new Assert\NotBlank())
        ));
        $builder->add('email','email',array(
            'label' => 'Email',
            'constraints' => array(new Assert\NotBlank())
        ));
        $builder->add('plainPassword','password',array(
            'label' => 'Contraseña',
            'constraints' => array(new Assert\NotBlank()),
            'attr' => array('placeholder' => 'Contraseña')
        ));
        $builder->add('dataConsent','checkbox',array(
            'required' => false,
            'mapped' => false,
            'constraints' => array(new True(array('message' => 'Acepte las condiciones')))));
    }

    public function getName()
    {
        return 'small_user_registration';
    }
}
