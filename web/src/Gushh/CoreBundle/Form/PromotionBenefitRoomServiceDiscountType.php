<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Gushh\CoreBundle\Entity\RoomServiceRepository;

class PromotionBenefitRoomServiceDiscountType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $room = $options['data']->getPromotion()->getRoom()->getId();

        $builder
            ->add('value', null, array(
                    'required'    => true,
                    'attr' => array(
                        'class'       => 'form-control',
                        )
                ))
            ->add('roomService', 'entity', array(
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:RoomService',
                    'query_builder' => function(RoomServiceRepository $er) use ( $room ) {
                        return $er->findByRoom($room);
                    }
                ))
            ->add('valueType', null, array(
                    'required'    => true,
                    'attr' => array(
                        'class'       => 'form-control',
                        )
                ))
            ->add('cumulative', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('eachValue', null, array(
                    'required'    => true,
                    'attr' => array(
                        'class'       => 'form-control',
                        'min'         => 1,
                        'max'         => 30
                        )
                ))
            ->add('hasLimit', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class'       => '',
                        )
                ))
            ->add('limitValue', null, array(
                    'required'    => true,
                    'attr' => [
                        'class'       => 'form-control',
                        'min'         => 1
                    ]
                ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\PromotionBenefit']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_promotionbenefit';
    }
}
