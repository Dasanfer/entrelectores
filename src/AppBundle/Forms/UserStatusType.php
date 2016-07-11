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

class UserStatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('text','textarea',array('label' => 'Mensaje de estado'));
    }

    public function getName()
    {
        return 'user_status';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\UserStatus',
            'csrf_protection' => false
        ));
    }
}
