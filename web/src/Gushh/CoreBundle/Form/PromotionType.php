<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Carbon\Carbon;

class PromotionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $now      = Carbon::now();

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
                        'placeholder' => 'Description',
                        'rows'        => '10'
                        )
                ))
            ->add('cutOff', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Cut Off',
                    'min'         => '0',
                )
            ))
            ->add('validFrom', 'text', array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'value'       => $now->format('d M Y')
                        )
                ))
            ->add('periodFrom', 'text', array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'value'       => $now->format('d M Y')
                        )
                ))
            ->add('validTo', 'text', array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'value'       => $now->addDay()->format('d M Y')
                        )
                ))
            ->add('periodTo', 'text', array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'value'       => $now->format('d M Y')
                        )
                ))
            ->add('combinable', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('availableInPremiumDates', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('availableInStopSellDates', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('nonRefundable', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('availableMonday', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('availableTuesday', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('availableWednesday', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('availableThursday', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('availableFriday', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('availableSaturday', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('availableSunday', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('enabled', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('exceptions', CollectionType::class, array(
                'required' => false,
                'entry_type' => PromotionExceptionPeriodType::class,
                'allow_add' => true,
                'by_reference' => false,
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\Promotion']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_promotion';
    }
}
