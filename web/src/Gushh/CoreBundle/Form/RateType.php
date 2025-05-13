<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RateType extends AbstractType
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
            ->add('occupancyTax', null, array(
                    'attr' => array(
                        'class'       => 'form-control blackOut',
                        'placeholder' => 'Occupancy Tax ($)'
                        )
                ))
            ->add('label', null, array(
                    'required'    => true,
                    'attr' => array(
                        'class'       => 'form-control',
                        )
                ))
            ->add('price', null, array(
                    'attr' => array(
                        'class'       => 'form-control blackOut',
                        'placeholder' => 'Price ($)'
                        )
                ))
            ->add('tax', null, array(
                    'attr' => array(
                        'class'       => 'form-control blackOut',
                        'placeholder' => 'Tax (%)'
                        )
                ))
            ->add('profitType', null, array(
                    'required'    => true,
                    'attr' => array(
                        'class'       => 'form-control blackOut',
                        )
                ))
            ->add('profit', null, array(
                    'attr' => array(
                        'class'       => 'form-control blackOut',
                        'placeholder' => 'Profit'
                        )
                ))
            ->add('priceTriple', null, array(
                    'attr' => array(
                        'class'       => 'form-control blackOut',
                        'placeholder' => 'Extra Person Price ($)'
                        )
                ))
            ->add('priceQuadruple', null, array(
                    'attr' => array(
                        'class'       => 'form-control blackOut',
                        'placeholder' => 'Extra Person Price ($)'
                        )
                ))
            ->add('priceQuintuple', null, array(
                    'attr' => array(
                        'class'       => 'form-control blackOut',
                        'placeholder' => 'Extra Person Price ($)'
                        )
                ))
            ->add('priceSextuple', null, array(
                    'attr' => array(
                        'class'       => 'form-control blackOut',
                        'placeholder' => 'Extra Person Price ($)'
                        )
                ))
            ->add('priceSeptuple', null, array(
                    'attr' => array(
                        'class'       => 'form-control blackOut',
                        'placeholder' => 'Extra Person Price ($)'
                        )
                ))
            ->add('priceOctuple', null, array(
                    'attr' => array(
                        'class'       => 'form-control blackOut',
                        'placeholder' => 'Extra Person Price ($)'
                        )
                ))
            // ->add('occupancyTax', null, array(
            //         'attr' => array(
            //             'class'       => 'form-control',
            //             'placeholder' => 'Price ($)'
            //             )
            //     ))
            // ->add('enabled')
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('profitType')
            // ->add('room')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\Rate']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_rate';
    }
}
