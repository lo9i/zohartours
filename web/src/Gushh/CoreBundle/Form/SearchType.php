<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hotel', 'hidden')
            ->add('city', 'hidden')
            ->add('state', 'hidden')
            ->add('country', 'hidden')
            ->add('continent', 'hidden')
            ->add('region', 'hidden')
            // ->add('rooms', 'hidden')
            ->add('checkIn', 'hidden')
            ->add('checkOut', 'hidden')
            ->add('room1Adults', 'hidden')
            ->add('room1Children', 'hidden')
            ->add('room1ChildrenAge', 'hidden')

            // ->add('room2Adults', 'hidden')
            // ->add('room2Children', 'hidden')
            // ->add('room2ChildrenAge', 'hidden')
            // ->add('room3Adults', 'hidden')
            // ->add('room3Children', 'hidden')
            // ->add('room3ChildrenAge', 'hidden')
            // ->add('room4Adults', 'hidden')
            // ->add('room4Children', 'hidden')
            // ->add('room4ChildrenAge', 'hidden')
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
            ['data_class' => 'Gushh\CoreBundle\Entity\Search']
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gushh_corebundle_search';
    }
}
