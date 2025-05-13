<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HotelCurrencyType extends AbstractType
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
            ->add('shortName', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Short Name (USD, EUR)'
                        )
                ))
            ->add('symbol', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Symbol ($, €)'
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
            ['data_class' => 'Gushh\CoreBundle\Entity\HotelCurrency']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_hotelcurrency';
    }
}
