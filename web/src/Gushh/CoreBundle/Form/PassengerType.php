<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\OptionsResolver\OptionsResolver;



class PassengerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'required'    => 'required',
                'attr' => array(
                    'class'       => 'form-control'
                )
            ))
            ->add('name', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Name'
                )
            ))
            ->add('lastname', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Lastname'
                )
            ))

            ->add('birthDate', 'text', array(
                'attr' => array(
                    'class'       => 'form-control birthDate',
                    'format' => 'dd M Y',
                )
            ))
        ;
        $builder->addEventListener(FormEvents::POST_SET_DATA, function ($event) {
            $passengerData = $event->getData();
            if( $passengerData ) {
                $birthDate = new \DateTime($passengerData->getBirthDate());
                $event->getForm()->get('birthDate')->setData($birthDate->format('d M Y'));
            }
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\Passenger']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_passenger';
    }
}
