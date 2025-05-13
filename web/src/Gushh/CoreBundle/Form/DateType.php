<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Gushh\CoreBundle\Entity\RateRepository;
use Gushh\CoreBundle\Entity\CancellationPolicyRepository;

class DateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $room  = $options['data']->getRoom()->getId();
        $hotel = $options['data']->getRoom()->getHotel()->getId();

        $now = new \DateTime('now');
        $now = $now->format('d-M-Y');

        $builder
            ->add('dateFrom', 'text', array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'value'       => $now
                        )
                ))

            ->add('dateTo', 'text', array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'value'       => $now
                        )
                ))

            ->add('stock', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Stock',
                        'min'         => '0',
                        )
                ))

            ->add('cutOff', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Cut Off',
                        'min'         => '0',
                        )
                ))

            ->add('minimumStay', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Minimum Stay',
                        'min'         => '1',
                        )
                ))

            ->add('maximumStay', null, array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Maximum Stay',
                        'min'         => '1',
                        )
                ))

            ->add('premiumDate', 'checkbox', array(
                    'required' => false
                ))

            ->add('rate', 'entity', array(
                    'required' => true,
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:Rate',
                    'query_builder' => function (RateRepository $er) use ($room) {
                        return $er->findByRoom($room);
                    }
                ))
            ->add('mondayRate', 'entity', array(
                    'required' => true,
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:Rate',
                    'query_builder' => function (RateRepository $er) use ($room) {
                        return $er->findByRoom($room);
                    }
                ))

            ->add('tuesdayRate', 'entity', array(
                    'required' => true,
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:Rate',
                    'query_builder' => function (RateRepository $er) use ($room) {
                        return $er->findByRoom($room);
                    }
                ))

            ->add('wednesdayRate', 'entity', array(
                    'required' => true,
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:Rate',
                    'query_builder' => function (RateRepository $er) use ($room) {
                        return $er->findByRoom($room);
                    }
                ))

            ->add('thursdayRate', 'entity', array(
                    'required' => true,
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:Rate',
                    'query_builder' => function (RateRepository $er) use ($room) {
                        return $er->findByRoom($room);
                    }
                ))

            ->add('fridayRate', 'entity', array(
                    'required' => true,
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:Rate',
                    'query_builder' => function (RateRepository $er) use ($room) {
                        return $er->findByRoom($room);
                    }
                ))

            ->add('saturdayRate', 'entity', array(
                    'required' => true,
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:Rate',
                    'query_builder' => function (RateRepository $er) use ($room) {
                        return $er->findByRoom($room);
                    }
                ))

            ->add('sundayRate', 'entity', array(
                    'required' => true,
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:Rate',
                    'query_builder' => function (RateRepository $er) use ($room) {
                        return $er->findByRoom($room);
                    }
                ))

            ->add('cancellationPolicy', 'entity', array(
                    'required' => true,
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:CancellationPolicy',
                    'query_builder' => function (CancellationPolicyRepository $er) use ($hotel) {
                        return $er->findByHotel($hotel);
                    }
                ))
            ->add('dailyRates', null, array(
                    'required'    => false,
                    'attr' => array(
                        'class'       => 'form-control',
                        'choice_label' => 'Daily rates.'
                        )
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
