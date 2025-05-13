<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Gushh\CoreBundle\Entity\PromotionBenefitTypeRepository;

class PromotionBenefitType extends AbstractType
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
            ->add('type', null, array(
                    'required'    => true,
                    'attr' => array(
                        'class'       => 'form-control',
                        )
                ))
            ->add('valueType', null, array(
                    'required'    => true,
                    'attr' => array(
                        'class'       => 'form-control',
                        )
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
        return 'gushh_corebundle_promotionbenefit';
    }
}
