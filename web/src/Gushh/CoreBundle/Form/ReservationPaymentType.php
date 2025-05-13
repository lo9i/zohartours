<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class ReservationPaymentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('amount', MoneyType::class, array(
                'invalid_message' => 'Amount is invalid!',
                'label'           => 'Enter Amount',
                'currency'        => $options['currency'],
                'error_bubbling'  => true,
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => '0.00',
                ),
//                'constraints' => array(
//                    new Regex( array( 'pattern' => '/[0-9]{1,}\.[0-9]{2}/')),
//                ),
            ))
            ->add('reference', 'text', array(
                'required' => false,
                'attr'     => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Internal reference'
                )
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\ReservationPayment',
             'currency'   => 'USD'
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_reservationpayment';
    }
}
