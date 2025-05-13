<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgencyType extends AbstractType
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
            ->add('address', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Address'
                        )
                ))
            ->add('city', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'City',
                )
            ))
            ->add('state', null, array(
                'required'    => false,
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'State',
                )
            ))
            ->add('country', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Country ',
                )
            ))
            ->add('zipCode', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Zip Code',
                )
            ))

            ->add('website', 'url', array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'http://misite.com'
                        )
                ))
            ->add('phone', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => '+1 212 123 444'
                        )
                ))
            ->add('fax', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => '+1 212 123 444'
                        )
                ))
            ->add('commission', null, array(
                    'attr' => array(
                        'class'         => 'form-control',
                        'placeholder'   => 'Commission (%)',
                        'min'           => '0',
                        'max'           => '25'
                        )
                ))
            ->add('taxpayerId', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'XXX-XX-XXXX'
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
            ['data_class' => 'Gushh\CoreBundle\Entity\Agency']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_agency';
    }
}
