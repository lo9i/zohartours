<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class EditPromotionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $validFrom = new \DateTime($options['data']->getValidFrom());
        $validFrom = $validFrom->format('d M Y');

        $validTo = new \DateTime($options['data']->getValidTo());
        $validTo = $validTo->format('d M Y');

        $periodFrom = new \DateTime($options['data']->getPeriodFrom());
        $periodFrom = $periodFrom->format('d M Y');

        $periodTo = new \DateTime($options['data']->getPeriodTo());
        $periodTo = $periodTo->format('d M Y');

        $builder
            ->add('name', null, array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Name'
                )
            ))
            ->add('description', null, array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Description',
                    'rows' => '10'
                )
            ))
            ->add('cutOff', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Cut Off'
                )
            ))
            ->add('validFrom', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'value' => $validFrom
                )
            ))
            ->add('validTo', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'value' => $validTo
                )
            ))
            ->add('periodFrom', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'value' => $periodFrom
                )
            ))
            ->add('periodTo', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'value' => $periodTo
                )
            ))
            ->add('combinable', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'class' => '',
                )
            ))
            ->add('availableInPremiumDates', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'class' => '',
                )
            ))
            ->add('availableInStopSellDates', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'class' => '',
                )
            ))
            ->add('nonRefundable', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'class' => '',
                )
            ))
            ->add('availableMonday', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'class' => '',
                )
            ))
            ->add('availableTuesday', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'class' => '',
                )
            ))
            ->add('availableWednesday', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'class' => '',
                )
            ))
            ->add('availableThursday', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'class' => '',
                )
            ))
            ->add('availableFriday', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'class' => '',
                )
            ))
            ->add('availableSaturday', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'class' => '',
                )
            ))
            ->add('availableSunday', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'class' => '',
                )
            ))
            ->add('enabled', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'class' => '',
                )
            ))

            ->add('exceptions', CollectionType::class, array(
                'required' => false,
                'entry_type' => PromotionExceptionPeriodType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
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
