<?php

namespace Gushh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HTML5DateType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        /*$resolver->setDefaults(array(
            'choices' => array(
                'm' => 'Male',
                'f' => 'Female',
            )
        ));*/

    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'html5date';
    }

}
