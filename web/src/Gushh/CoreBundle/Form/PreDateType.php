<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Gushh\CoreBundle\Entity\RateRepository;

class DateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $room = $options['data']->getRoom()->getId();

        $builder
            ->add('name', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'name'
                        )
                ))
            ->add('dateFrom')
            ->add('dateTo')
            ->add('stock', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Stock'
                        )
                ))
            ->add('cutOff', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Cut Off'
                        )
                ))
            ->add('minimumStay', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Minimum Stay'
                        )
                ))
            ->add('maximumStay', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Maximum Stay'
                        )
                ))
            ->add('premiumDate', 'checkbox', array(
                    'attr' => array(
                        'class'       => '',
                        'required'  => false
                        )
                ))
            // ->add('rate', null, array(
            //         'attr' => array(
            //             'class'       => 'form-control'
            //             )
            //     ))
            
            ->add('rate', 'entity', array(
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:Rate',
                    'query_builder' => function(RateRepository $er) use ( $room ) {
                        return $er->findByRoom($room);
                    }
                ))
            // ->add('enabled')
            // ->add('stopSell')
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('room')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\Date']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_date';
    }
}
