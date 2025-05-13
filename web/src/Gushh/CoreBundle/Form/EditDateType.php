<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Gushh\CoreBundle\Entity\RateRepository;
use Gushh\CoreBundle\Entity\CancellationPolicyRepository;

class EditDateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $room  = $options['data']->getRoom()->getId();
        $hotel = $options['data']->getRoom()->getHotel()->getId();

        $builder
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
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('stopSell', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))            
            ->add('rate', 'entity', array(
                    'required' => true,
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:Rate',
                    'query_builder' => function(RateRepository $er) use ( $room ) {
                        return $er->findByRoom($room);
                    }
                ))
            ->add('cancellationPolicy', 'entity', array(
                    'required' => true,
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:CancellationPolicy',
                    'query_builder' => function(CancellationPolicyRepository $er) use ( $hotel ) {
                        return $er->findByHotel($hotel);
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
        return 'gushh_corebundle_editdate';
    }
}
