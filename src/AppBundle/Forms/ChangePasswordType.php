<?php
// src/AppBundle/Form/Type/TaskType.php
namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Forms\DataTransformer\BookToIntTransformer;
use AppBundle\Forms\DataTransformer\CommentToIntTransformer;
use AppBundle\Forms\DataTransformer\BookListToIntTransformer;
use AppBundle\Forms\DataTransformer\AuthorToIntTransformer;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('current_password', 'password', array(
            'label' => 'Contraseña antigua',
            'required' => false,
            'mapped' => false,
            'constraints' => new UserPassword(),
        ));

        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'Las contraseñas deben coincidir.',
            'options' => array('attr' => array('class' => 'password-field')),
            'required' => false,
            'first_options'  => array('label' => 'Contraseña nueva'),
            'second_options' => array('label' => 'Repetir contraseña'),
        ));
    }

    public function getName()
    {
        return 'user_password';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }
}
