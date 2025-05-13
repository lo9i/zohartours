<?php

namespace Gushh\FrontendBundle\Controller;

use Gushh\CoreBundle\Classes\SearchCore;
use Gushh\CoreBundle\Form\SearchType;
use Gushh\CoreBundle\Classes\Util;
use Gushh\CoreBundle\Entity\Search;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
   
    /**
     * @Route("/search/hotels/{region}/{continent}/{country}/{state}/{city}/{hotel}/{scode}/", requirements={"continent" = "america|europe"}, defaults={"region" = "-","country" = "-", "state" = "-", "city" = "-", "hotel" = "-"}, name="search_hotels")
     * @Template()
     * @Method("GET")
     */
    public function searchHotelsAction(Request $request, $region, $continent, $country, $state, $city, $hotel, $scode)
    {
        $city    = $city != '-' ? str_replace('-', ' ', $city) : '';
        $state    = $state != '-' ? str_replace('-', ' ', $state) : '';
        $country = $country != '-' ? str_replace('-', ' ', $country) : '';
        $region = $region != '-' ? str_replace('-', ' ', $region) : '';
        $hotel   = $hotel != '-' ? str_replace('-', ' ', $hotel) : '';

        $em     = $this->getDoctrine()->getManager();
        $user =  $this->getUser();
        $search = $em->getRepository('GushhCoreBundle:Search')->findSearchBy($region, $continent, $country, $state, $city, $hotel, $scode, $user);

        if (!$search)
            return $this->redirect($this->generateUrl('home'));

        $searchCore = new SearchCore($search, $user, $em, $request->getLocale());
        $hotelCount = 0;
        $hotels     = $searchCore->findHotels($hotelCount);

        $isVip        = $user != null && $user->getVip();
        $cities       = $em->getRepository('GushhCoreBundle:Hotel')->findStates('america', 'United States', 2, $isVip);
        $countries    = $em->getRepository('GushhCoreBundle:Hotel')->findStates('europe', null, 2, $isVip);
        $allHotels    = $em->getRepository('GushhCoreBundle:Hotel')->findAllHotels($isVip);
        $regions      = $em->getRepository('GushhCoreBundle:Hotel')->findRegions();

        $footerLinks= $em->getRepository('GushhCoreBundle:StaticPage')->findBy(['enabled' => true]);

        $form   = $this->createForm(new SearchType(), $search, array(
            'action' => $this->generateUrl('home'),
            'attr' => array(
                'id'    => 'searchForm',
                'class' => 'ui form',
                'accept-charset' => 'UTF-8 ISO-8859-1'
            ),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        return [
            'activePage'    => 'hotels',
            'hotels'        => $hotels,
            'hotelCount'    => $hotelCount,
            'regions'       => $regions,
            'search'        => $search,
            'searchForm'    => $form->createView(),
            'cities'        => $cities,
            'countries'     => $countries,
            'allHotels'     => $allHotels,
            'footerLinks'   => $footerLinks,
            'city'          => $search->getCity(),
            'state'         => $search->getState(),
            'country'       => $search->getCountry(),
            'continent'     => $search->getContinent(),
            'region'        => $search->getRegion()
        ];
    }
}
