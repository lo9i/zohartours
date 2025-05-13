<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckOutConfirmationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('confirmed', 'checkbox', array(
                    'required'    => 'required',
                    'attr' => array(
                        'class'       => 'form-control'
                        )
                ))
            // ->add('code')
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('user')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\CheckOut']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_checkoutconfirmation';
    }
}
