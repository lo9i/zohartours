<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientFileItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('amount_sale', MoneyType::class, array(
                'invalid_message' => 'Amount Sales is invalid!',
                'label'           => 'Enter Sales Amount',
                'currency'        => $options['currency'],
                'error_bubbling'  => true,
                'attr' => array(
                    'placeholder' => '0.00',
                    'min'         => '0',
                    'class'       => 'has-to-be-number sale',
                    'style'       => 'width:90%; text-align:right;'
                ),
            ))
            ->add('amount_net', MoneyType::class, array(
                'invalid_message' => 'Amount Net is invalid!',
                'label'           => 'Enter Net Amount',
                'currency'        => $options['currency'],
                'error_bubbling'  => true,
                'attr' => array(
                    'placeholder' => '0.00',
                    'min'         => '0',
                    'class'       => 'has-to-be-number net',
                    'style'       => 'width:90%; text-align:right;'
                ),
            ))
            ->add('amount_commission', MoneyType::class, array(
                'invalid_message' => 'Amount Commission is invalid!',
                'label'           => 'Enter Commission Amount',
                'currency'        => $options['currency'],
                'error_bubbling'  => true,
                'attr' => array(
                    'placeholder' => '0.00',
                    'min'         => '0',
                    'class'       => 'has-to-be-number commission',
                    'style'       => 'width:90%; text-align:right;'
                ),
            ))
            ->add('provider', 'entity', array(
                'required' => true,
                'attr' => array(
                    'class'       => 'form-control',
                ),
                'class' => 'GushhCoreBundle:Provider',
                'choice_label' => 'name',
            ))
            ->add('detail', TextareaType::class, array(
                'required' => true,
                'attr'     => array(
                    'class'       => 'form-control',
                    'rows'        => '5',
                    'placeholder' => 'Detail'
                )
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\ClientFileItem',
             'currency'   => 'USD'
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_client_file_item';
    }
}
