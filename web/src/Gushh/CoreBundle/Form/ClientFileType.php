<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientFileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('clientName', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Client First Name',
                )
            ))
            ->add('clientLastName', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Client Last Name',
                )
            ))
            ->add('agency', 'entity', array(
                'required' => true,
                'class' => 'GushhCoreBundle:Agency',
                'attr' => array(
                    'class'       => 'form-control',
                )
            ))
            ->add('notes', null, array(
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Notes to include in the file'
                )
            ))
            ->add('items', CollectionType::class, array(
                'required' => false,
                'entry_type' => ClientFileItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\ClientFile']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_client_file';
    }
}
