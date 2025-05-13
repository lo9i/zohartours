<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Gushh\CoreBundle\Form\PassengerType;

class CheckOutType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             ->add('notes', 'textarea', array(
                    'required' => false,
                     'attr' => array(
                         'class'       => 'form-control',
                         'placeholder' => 'Notes'
                         )
                 ))
            ->add('guests', CollectionType::class, array(
                'required' => true,
                'entry_type' => PassengerType::class,
                'allow_add' => true,
                'by_reference' => false,
                'label' => false
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
        return 'gushh_corebundle_checkout';
    }
}
