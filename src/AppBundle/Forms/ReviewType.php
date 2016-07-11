<?php
// src/AppBundle/Form/Type/TaskType.php
namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Forms\DataTransformer\BookToIntTransformer;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bookTransformer = new BookToIntTransformer($options["em"]);

        $builder
            ->add('title','text')
            ->add('text','textarea')
            ->add('spoiler','checkbox',array('label' => null, 'required' => false))
            ->add($builder->create('book', 'hidden')->addModelTransformer($bookTransformer));
    }

    public function getName()
    {
        return '';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Review',
            'csrf_protection' => false,
            'em' => null
        ));
    }
}
