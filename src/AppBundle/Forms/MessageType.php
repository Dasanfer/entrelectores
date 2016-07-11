<?php
// src/AppBundle/Form/Type/TaskType.php
namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Forms\DataTransformer\UserToIntTransformer;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userTransformer = new UserToIntTransformer($options["em"]);

        $builder
            ->add('text','textarea')
            ->add($builder->create('to', 'hidden')->addModelTransformer($userTransformer));
    }

    public function getName()
    {
        return '';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Message',
            'csrf_protection' => false,
            'em' => null
        ));
    }
}
