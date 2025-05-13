<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AmenityType extends AbstractType
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
                    'required'  => false,
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Description'
                        )
                ))
            // ->add('enabled')
            // ->add('slug')
            // ->add('createdAt')
            // ->add('updatedAt')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\Amenity']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_hotel_amenity';
    }
}
