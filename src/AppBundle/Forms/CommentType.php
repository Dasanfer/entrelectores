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

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bookTransformer = new BookToIntTransformer($options["em"]);
        $commentTransformer = new CommentToIntTransformer($options["em"]);
        $listTransformer = new BookListToIntTransformer($options["em"]);
        $authorTransformer = new AuthorToIntTransformer($options["em"]);

        $builder
            ->add('content','textarea')
            ->add($builder->create('parent', 'hidden')->addModelTransformer($commentTransformer))
            ->add($builder->create('book', 'hidden')->addModelTransformer($bookTransformer))
            ->add($builder->create('author', 'hidden')->addModelTransformer($authorTransformer))
            ->add($builder->create('list', 'hidden')->addModelTransformer($listTransformer));
    }

    public function getName()
    {
        return '';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Comment',
            'csrf_protection' => false,
            'em' => null
        ));
    }
}
