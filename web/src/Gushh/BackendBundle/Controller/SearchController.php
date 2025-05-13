<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * Dashboard controller.
 *
 * @Route("/search")
 */
class SearchController extends Controller
{
    /**
     * @Route("/hotels/", name="backend_search_hotels")
     * @Method("GET")
     * @Template()
     */
    public function hotelsAction()
    {

      $em = $this->getDoctrine()->getManager();

      $usaCities     = $em->getRepository('GushhCoreBundle:Hotel')->findStates('america', 'United States', null, $this->getUser()->getVip());
      $europeCities  = $em->getRepository('GushhCoreBundle:Hotel')->findStates('europe', null, null, $this->getUser()->getVip());

      $cities = [
          'usa'       => $usaCities,
          'europe'    => $europeCities,
      ];
      

      return [
        'cities'  =>  $cities,
      ];
    }

}
