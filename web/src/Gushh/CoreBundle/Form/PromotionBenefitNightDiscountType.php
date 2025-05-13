<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Gushh\CoreBundle\Entity\PromotionBenefitTypeRepository;

class PromotionBenefitNightDiscountType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', null, array(
                    'required'    => true,
                    'attr' => array(
                        'class'       => 'form-control',
                        )
                ))
            // ->add('type', 'entity', array(
            //         'attr' => array(
            //             'class'       => 'form-control'
            //             ),
            //         'class' => 'GushhCoreBundle:PromotionBenefitType',
            //         'query_builder' => function(PromotionBenefitTypeRepository $er) {
            //             return $er->findBySlug('night-discount');
            //         }
            //     ))
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
                ))
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('promotion')
        ;
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
        return 'gushh_corebundle_promotionnightbenefit';
    }
}
