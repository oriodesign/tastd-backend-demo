<?php
namespace Tastd\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class LoginFormType
 *
 * @package Tastd\Bundle\CoreBundle\Form\Type
 */
class LoginFormType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('provider', 'text')
            ->add('email', 'email', array('label' => 'Email'))
            ->add('password', 'password', array('label' => 'Password'))
            ->add('credential', 'text', array('mapped' => false ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'=>false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tastd_login';
    }
} 