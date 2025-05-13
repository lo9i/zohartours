<?php

namespace Gushh\CoreBundle\Form;

use Gushh\CoreBundle\Entity\ReservationPaymentStatusRepository;
use Gushh\CoreBundle\Entity\ReservationStatusRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ReservationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (in_array('ROLE_ADMIN', $options['role']) || in_array('ROLE_SUPER_ADMIN', $options['role'])) {
            $builder
                ->add('hotelFileId', null, array(
                    'required' => false,
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'H-xxxxx-xxx'
                    )
                ))
                ->add('remarks', null, array(
                    'required' => false,
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Notes to include in Voucher'
                    )
                ))
                ->add('paymentStatus', 'entity', array(
                    'required' => true,
                    'attr' => array(
                        'class' => 'form-control'
                    ),
                    'class' => 'GushhCoreBundle:ReservationPaymentStatus',
                    'query_builder' => function (ReservationPaymentStatusRepository $er) {
                        return $er->findAll();
                    }
                ))

                ->add('reservationDates', CollectionType::class, array(
                    'required' => false,
                    'entry_type' => ReservationDateType::class,
                    'allow_add' => false,
                    'by_reference' => false,
                    'label' => false,
                ))

                ->add('payments', CollectionType::class, array(
                    'required' => false,
                    'entry_type' => ReservationPaymentType::class,
                    'allow_add' => true,
                    'by_reference' => false,
                    'label' => false,
                ))
            ;
        }

        $builder->add('status', 'entity', array(
            'required' => true,
            'attr' => array(
                'class' => 'form-control'
            ),
            'class' => 'GushhCoreBundle:ReservationStatus',
            'query_builder' => function (ReservationStatusRepository $er) {
                return $er->findAll();
            }
        ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gushh\CoreBundle\Entity\Reservation',
            'role' => null,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_reservation';
    }
}
