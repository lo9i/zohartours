<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HotelPromotionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Title'
                        )
                ))
            ->add('description', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'rows'        => '10',
                        'placeholder' => 'Description'
                        )
                ))
        ;
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
            ['data_class' => 'Gushh\CoreBundle\Entity\HotelPromotion']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_hotelpromotion';
    }
}
