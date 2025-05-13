<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'hidden')
            ->add('name', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Name'
                        )
                ))
            ->add('lastname', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Lastname'
                        )
                ))
            ->add('agency', null, array(
                    'required' => false,
                    'attr' => array(
                        'class'       => 'form-control',
                        )
                ))
            ->add('email', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Email'
                        )
                ))
            // ->add('submit', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\User']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_user';
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

}
