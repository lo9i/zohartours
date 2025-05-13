<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaticPageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enTitle', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Title'
                        )
                ))
            ->add('enContent', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Content',
                        'rows'        => '20'
                        )
                ))
            ->add('esTitle', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Title'
                        )
                ))
            ->add('esContent', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Content',
                        'rows'        => '20'
                        )
                ))
            ->add('enabled', null, array(
                    'required'    => false,
                    'attr' => array(
                        'class'       => 'form-control',
                        'choice_label' => 'Enable or disable this page.'
                        )
                ))
            // ->add('description')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\StaticPage']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_static_page';
    }
}
