<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomServiceType extends AbstractType
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
            ->add('description', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'rows'        => '10',
                        'placeholder' => 'Description'
                        )
                ))
            ->add('price', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Price'
                        )
                ))
            ->add('tax', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Tax (%)'
                        )
                ))
            ->add('profitType', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'required'    => 'required'
                        )
                ))
            ->add('profit', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Profit'
                        )
                ))
            ->add('serviceType', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'required'    => 'required'
                        )
                ))
            ->add('servicePayType', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'required'    => 'required'
                        )
                ))
            // ->add('enabled')
            // ->add('slug')
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
            ['data_class' => 'Gushh\CoreBundle\Entity\RoomService']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_roomservice';
    }
}
