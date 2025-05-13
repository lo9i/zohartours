<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HotelType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Name'
                )
            ))
            ->add('subtitle', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Subtitle'
                )
            ))
            ->add('enDescription', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'rows'        => '10',
                    'placeholder' => 'English Description'
                )
            ))
            ->add('esDescription', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'rows'        => '10',
                    'placeholder' => 'Spanish Description'
                )
            ))
            ->add('address', null, array(
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Address',
                    'autocomplete' => 'off'
                )
            ))
            ->add(
                'phone',
                null,
                array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Phone',
                        'autocomplete' => 'off'
                    )
                )
            )
            ->add(
                'website',
                'url',
                array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'http://misite.com'
                    )
                )
            )
            ->add(
                'childAge',
                'integer',
                array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder'   => 'Free children age',
                        'autocomplete'  => 'off',
                        'min'           => 0,
                        'max'           => 17
                    )
                )
            )
            ->add(
                'city',
                null,
                array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'City (Autocomplete)',
                        //                         'readonly' => ''
                    )
                )
            )
            ->add(
                'state',
                null,
                array(
                    'required'    => false,
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'State (Autocomplete)',
                        //                         'readonly' => ''
                    )
                )
            )
            ->add(
                'country',
                null,
                array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Country (Autocomplete)',
                        //                       'readonly' => ''
                    )
                )
            )
            ->add(
                'zipCode',
                null,
                array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Zip Code (Autocomplete)',
                        //                         'readonly' => ''
                    )
                )
            )
            ->add(
                'region',
                null,
                array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Region',
                        //                    'readonly' => ''
                    )
                )
            )
            ->add(
                'checkIn',
                'html5time',
                array(
                    'attr' => array(
                        'class'       => 'form-control'
                    )
                )
            )
            ->add(
                'checkOut',
                'html5time',
                array(
                    'attr' => array(
                        'class'       => 'form-control'
                    )
                )
            )
            ->add(
                'reservationEmail',
                'email',
                array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Reservation email'
                    )
                )
            )
            ->add(
                'cancellationEmail',
                'email',
                array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Cancellation email'
                    )
                )
            )
            ->add(
                'coords',
                'hidden',
                array(
                    'attr' => array(
                        'class'       => 'form-control',
                        'placeholder' => 'Google Maps Coords (Autocomplete)',
                        'readonly' => ''
                    )
                )
            )
            ->add(
                'currency',
                null,
                array(
                    'required'    => 'required',
                    'attr' => array(
                        'class'       => 'form-control'
                    )
                )
            )
            ->add(
                'amenities',
                'entity',
                array(
                    'class' => 'GushhCoreBundle:Amenity',
                    'choice_label' => 'name',
                    'expanded'     => true,
                    'multiple'     => true,
                    'attr'         => array(
                        'class'       => 'form-control'
                    )
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'required'    => false,
                    'attr' => array(
                        'class'       => 'form-control',
                        'choice_label' => 'Enable or disable this Hotel.'
                    )
                )
            )
            ->add('vip', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'class'       => 'form-control',
                )
            ))
            ->add('video', null, array(
                'required'    => false,
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Video',
                )
            ))
            ->add('hotelbedsId', null, array(
                'required'    => false,
                'attr' => array(
                    'class'       => 'form-control',
                    'placeholder' => 'Hotelbeds id',
                )
            ))
            ->add('enVoucherNote', null, array(
                'required'    => false,
                'attr' => array(
                    'class'       => 'form-control',
                    'rows'        => '5',
                    'placeholder' => 'English Voucher Note'
                )
            ))
            // ->add('esVoucherNote', null, array(
            //         'required'    => false,
            //         'attr' => array(
            //             'class'       => 'form-control',
            //             'rows'        => '5',
            //             'placeholder' => 'Spanish Voucher Note'
            //             )
            //     ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Gushh\CoreBundle\Entity\Hotel']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_hotel';
    }
}
