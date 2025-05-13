<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Gushh\CoreBundle\Entity\PromotionExceptionPeriod;

class PromotionExceptionPeriodType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('periodFrom', 'text', array(
                'attr' => array(
                    'class'       => 'form-control periodFrom',
                    'format' => 'dd M Y',
                )
            ))
            ->add('periodTo', 'text', array(
                'attr' => array(
                    'class'       => 'form-control periodTo',
                    'format' => 'dd M Y',
                )
            ))
        ;
        $builder->addEventListener(FormEvents::POST_SET_DATA, function ($event) {
            $exceptionPeriod = $event->getData();
            if( $exceptionPeriod ) {
                $periodFrom = new \DateTime($exceptionPeriod->getPeriodFrom());
                $event->getForm()->get('periodFrom')->setData($periodFrom->format('d M Y'));
                $periodTo = new \DateTime($exceptionPeriod->getPeriodTo());
                $event->getForm()->get('periodTo')->setData($periodTo->format('d M Y'));
            }
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\PromotionExceptionPeriod']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_promotionexceptionperiod';
    }
}
