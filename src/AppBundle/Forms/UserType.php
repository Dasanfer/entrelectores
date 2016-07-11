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

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('file','file',array('label' => 'Foto de perfil','required' => false));
        $builder->add('name',null,array('label' => 'Nombre completo','required' => false));
        $builder->add('email','email',array('label' => 'Email'));

        $builder->add('gender','choice',
            array(
                'label' => 'Sexo',
                'choices' => array('M' => 'Hombre', 'F' => 'Mujer'),
                'required' => true,
                'attr' => array('class' => 'select_box')
            )
        );

        $builder->add('country',null,array('label' => 'País','required' => false));
        $builder->add('city',null,array('label' => 'Ciudad','required' => false));

        $builder->add('birthday','birthday',
            array(
                'label' => 'Fecha de nacimiento',
                'years' => range(date("Y")-100,date("Y"))
            )
        );

        $builder->add('cita','textarea',
            array(
                'label' => 'Cita',
                'required' => false
            )
        );

        $builder->add('publicProfile','hidden');
    }

    public function getName()
    {
        return 'user_profile';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }
}
