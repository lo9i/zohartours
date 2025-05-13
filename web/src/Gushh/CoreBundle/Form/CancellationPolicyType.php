<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CancellationPolicyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Name'
                        )
                ))
            ->add('cutOff', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Cut Off (days)',
                        'min' => 1
                        )
                ))
            ->add('cancellationPolicyType', null, array(
                    'attr' => array(
                        'class'       => 'form-control'
                        )
                ))
            ->add('penalty', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Penalty',
                        'min' => 0
                        )
                ))
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('hotel')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\CancellationPolicy']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_cancellationpolicy';
    }
}
