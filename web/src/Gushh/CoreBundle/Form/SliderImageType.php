<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SliderImageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', null, array(
                    'attr' => array(
                        'class'             => 'dropify',
                        'data-plugin'       => 'dropify',
                        // 'required'          => 'required'
                        // 'class'       => 'form-control',
                        // 'placeholder' => 'Name'
                        )
                ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\SliderImage']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_sliderimage';
    }
}
