<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Gushh\CoreBundle\Entity\PromotionConditionExpressionRepository;
use Gushh\CoreBundle\Entity\PromotionConditionConditionalRepository;

class PromotionConditionCumulativeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('conditional', 'entity', array(
                    'required'    => true,
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:PromotionConditionConditional',
                    'query_builder' => function(PromotionConditionConditionalRepository $er) {
                        return $er->findGroup('logic');
                    }
                ))
            ->add('expression', 'entity', array(
                    'required'    => true,
                    'attr' => array(
                        'class'       => 'form-control'
                        ),
                    'class' => 'GushhCoreBundle:PromotionConditionExpression',
                    'query_builder' => function(PromotionConditionExpressionRepository $er) {
                        return $er->findGroup('logic');
                    }
                ))
            ->add('value', 'integer', array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'min'         => 0,
                        'max'         => 99,
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
            ['data_class' => 'Gushh\CoreBundle\Entity\PromotionCondition']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_promotioncondition';
    }
}
