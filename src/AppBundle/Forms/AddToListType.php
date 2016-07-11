<?php
// src/AppBundle/Form/Type/TaskType.php
namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class AddToListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newName','text')
            ->add('list','entity',array('class' => 'AppBundle\Entity\BookList',
                'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u');
            }))
                ->add('book','entity',array('class' => 'AppBundle\Entity\Book',
                    'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u');
            }));
    }

    public function getName()
    {
        return 'add_to_list';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false
        ));
    }
}
