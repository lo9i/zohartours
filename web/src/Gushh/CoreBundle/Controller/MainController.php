<?php

namespace Gushh\CoreBundle\Controller;

use Gushh\CoreBundle\Classes\Util;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;


class MainController extends Controller
{
    /**
     * @Route("/pdf", name="core_homea")
     */
    public function indexAction(Request $request)
    {

        $args = [
            'reservation'   => [
                'action'    =>  'Modify Request',
                'code'      =>  U
            ],
            'email'         => [
                'subtitle'  => 'Please modify <strong>GUEST NAME</strong>'
            ]
        ];
        
        return new Response($this->renderView('Email/newRequest.html.twig', $args));
    }
    
}
