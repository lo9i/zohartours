<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientFileCommissionPaidType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $now = new \DateTime('now');
        $now = $now->format('dd M Y');

        $builder
            ->add('amount', MoneyType::class, array(
                'invalid_message' => 'Amount is invalid!',
                'label'           => 'Enter Amount',
                'currency'        => $options['currency'],
                'error_bubbling'  => true,
                'attr' => array(
                    'placeholder' => '0.00',
                    'min'         => '0',
                    'class'       => 'has-to-be-number',
                    'style'       => 'text-align:right;'
                ),
            ))
            ->add('date', 'text', array(
                'attr' => array(
                    'class'       => 'form-control date',
                    'value'       => $now,
                    'format' => 'dd M Y',
                )
            ))
            ->add('detail', TextareaType::class, array(
                'required' => true,
                'attr'     => array(
                    'class'       => 'form-control',
                    'rows'        => '5',
                    'placeholder' => 'Detail'
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
            ['data_class' => 'Gushh\CoreBundle\Entity\ClientFileCommissionPaid',
             'currency'   => 'USD'
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_client_file_commission_paid';
    }
}
