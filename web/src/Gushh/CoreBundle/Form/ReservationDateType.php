<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationDateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('totalWithServicesNet', null, array(
                    'attr' => array(
                        'class'       => 'form-control has-to-be-number is-net',
                        'placeholder' => 'Cost Final (includes services)',
                        'min'         => '0',
                        'style'       => 'width: 150px; margin: auto;'
                        )
                    )
                )
            ->add('totalWithServicesAndCommission', null, array(
                    'attr' => array(
                        'class'       => 'form-control has-to-be-number is-final',
                        'placeholder' => 'Price Final (includes services and commission)',
                        'min'         => '0',
                        'style'       => 'width: 150px; margin: auto;'
                    )
                )
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\ReservationDate']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_reservationdate';
    }
}
