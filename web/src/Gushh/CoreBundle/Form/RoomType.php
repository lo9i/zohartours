<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
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
            ->add('enDescription', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'rows'        => '10',
                        'placeholder' => 'English Description'
                        )
                ))
            ->add('esDescription', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'rows'        => '10',
                        'placeholder' => 'Spanish Description'
                        )
                ))
            ->add('capacity', 'integer', array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Capacity (PAX)',
                        'min' => 1,
                        'max' => 8
                        )
                ))
            ->add('squareFeet', 'integer', array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Square Feet',
                        'min' => 0,
                        'max' => 5000
                        )
                ))
            ->add('lowStockWarning', 'integer', array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Square Feet',
                        'min' => 0,
                        'max' => 20
                        )
                ))
            ->add('enabled', null, array(
                'required'    => false,
                'attr' => array(
                    'class'       => 'form-control',
                    'choice_label' => 'Enable or disable this Room.'
                )
            ))
            ->add('contractType', 'entity', array(
                'required' => true,
                'attr' => array(
                    'class'       => 'form-control'
                ),
                'class' => 'GushhCoreBundle:RoomContractType'
            ))
            // ->add('nextAction', 'hidden')

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
            ['data_class' => 'Gushh\CoreBundle\Entity\Room']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_room';
    }
}
