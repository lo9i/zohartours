<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('payerName', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Payer Name',
                )
            ))
            ->add('payerState', null, array(
                'required'    => true,
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'State',
                )
            ))
            ->add('payerAddress', null, array(
                'required'    => true,
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Address',
                )
            ))
            ->add('payerZipCode', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Zip Code',
                )
            ))

            ->add('payerCity', null, array(
                'required'    => true,
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'City',
                )
            ))
            ->add('payerCountry', null, array(
                'required'    => true,
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Country',
                )
            ))
            ->add('payerTaxId', null, array(
                'required'    => false,
                'empty_data' => '',
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Taxpayer ID',
                )
            ))
            ->add('notes', null, array(
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Notes to include in Invoice'
                )
            ))
            ->add('items', CollectionType::class, array(
                'required' => false,
                'entry_type' => InvoiceItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ))
//            ->add('invoiceId')
//            ->add('tax')
//            ->add('total')
//            ->add('createdAt')
//            ->add('updatedAt')
//            ->add('status')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\Invoice']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_invoice';
    }
}
