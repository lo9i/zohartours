<?php

namespace Gushh\FrontendBundle\Controller;

use Gushh\CoreBundle\Classes\Util;
use Gushh\CoreBundle\Entity\Search;
use Gushh\CoreBundle\Form\SearchType;
use Carbon\Carbon;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LoggerInterface;

class MainController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em           = $this->getDoctrine()->getManager();

        $search = new Search();

        $form   = $this->createForm(new SearchType(), $search, array(
            'action' => $this->generateUrl('home'),
            'attr' => array(
                'id'    => 'searchForm',
                'class' => 'ui form'
                ),
            'method' => 'POST',
        )); 
         $user = $this->getUser();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $hotel = str_replace(" ", "-", strtolower($search->getHotel()));
            $city = str_replace(" ", "-", strtolower($search->getCity()));
            $state = str_replace(" ", "-", strtolower($search->getState()));
            $country = str_replace(" ", "-", strtolower($search->getCountry()));
            $region = str_replace(" ", "-", strtolower($search->getRegion()));
            $days = Util::createRange($search->getCheckIn(), $search->getCheckOut(), false);
            $nights = count($days);
            $search->setNights($nights);
            $search->setUser($user);

            if($search->getHotel() == null)
                $search->setHotel('');
            else
                $search->setHotel(str_replace('-', ' ', $search->getHotel()));

            if($search->getCity() == null)
                $search->setCity('');
            else
                $search->setCity(str_replace('-', ' ', $search->getCity()));

            if($search->getState() == null)
                $search->setState('');
            else
                $search->setState(str_replace('-', ' ', $search->getState()));

            if($search->getCountry() == null || $search->getCountry() == 'EUROPE') {
                $search->setCountry('');
                $country = '';
            }
            if($search->getRegion() == null) {
                $search->setRegion('');
                $region = '';
            }


            $em->persist($search);
            $em->flush();

            $scode = $search->getCode();
            $continent = str_replace(" ", "-", strtolower($search->getContinent()));

            if($hotel == '') $hotel = '-' ;
            if($city == '') $city = '-' ;
            if($state == '') $state = '-' ;
            if($country == '') $country = '-' ;
            if($region == '') $region = '-' ;

            return $this->redirect($this->generateUrl('search_hotels', [
                    'region' => $region,
                    'continent' => $continent,
                    'country' => $country,
                    'state' => $state,
                    'city' => $city,
                    'hotel' => $hotel,
                    'scode' => $scode,
                ]
            ));
        }

        $footerLinks  = $em->getRepository('GushhCoreBundle:StaticPage')->findBy(['enabled' => true]);
        $cities       = $em->getRepository('GushhCoreBundle:Hotel')->findStates('america', 'United States', 2, false);
        $countries    = $em->getRepository('GushhCoreBundle:Hotel')->findStates('europe', null, 2, false);
        $regions      = $em->getRepository('GushhCoreBundle:Hotel')->findRegions();
        $hotels       = $em->getRepository('GushhCoreBundle:Hotel')->findAllHotels($user != null && $user->getVip());
        $mexCities    =  $em->getRepository('GushhCoreBundle:Hotel')->findStates('america', 'mexico', 2, false);
        $argCities    =  $em->getRepository('GushhCoreBundle:Hotel')->findStates('america', 'argentina', 2, false);
        return [
            'activePage' => 'home',
            'cities' => $cities,
            'mexCities' => $mexCities,
            'argCities' => $argCities,
            'countries' => $countries,
            'regions' => $regions,
            'hotels' => $hotels,
            'searchForm'   => $form->createView(),
            'footerLinks'=> $footerLinks,
        ];
    }

    /**
     * @Route("/tours/{t}/", name="tours")
     * @Template()
     */
    public function toursAction($t)
    {
        
        if($t == 'miami')
            $file = "./backend/toursMiami.html";
        else if($t == 'florida')
            $file = "./backend/toursFlorida.html";
        else if($t == 'newyork')
            $file = "./backend/toursNewYork.html";
        else
            $file = "./backend/tours.html";

        $logger = $this->get('logger');
        $logger->error($file);

        if (file_exists($file)) {
            return new Response(file_get_contents($file));
        } else {
            throw NotFoundHttpException($file);
        }
        /*

         $em         = $this->getDoctrine()->getManager();
                $footerLinks= $em->getRepository('GushhCoreBundle:StaticPage')->findBy(['enabled' => true]);

                return [
                    'activePage' => 'tours',
                    'footerLinks'=> $footerLinks,
                ];
        */
    }

    /**
     * @Route("/transfers/", name="transfers")
     * @Template()
     */
    public function transfersAction()
    {
        $file = "./backend/transfers.html";
        if (file_exists($file)) {
            return new Response(file_get_contents($file));
        } else {
            throw $this->createNotFoundException($file);
        }

//        $em         = $this->getDoctrine()->getManager();
//        if ($this->get('request')->getLocale() == 'es') {
//            $page        = $em->getRepository('GushhCoreBundle:StaticPage')->findOneByEsSlug('transferencias');
//        } else {
//            $page        = $em->getRepository('GushhCoreBundle:StaticPage')->findOneByEnSlug('transfers');
//        }
//        $footerLinks = $em->getRepository('GushhCoreBundle:StaticPage')->findBy(['enabled' => true]);
//
//        return [
//            'entity'     => $page,
//            'activePage' => 'transfers',
//            'footerLinks'=> $footerLinks,
//        ];
    }

    /**
     * @Route("/cruises/", name="cruises")
     * @Template()
     */
    public function cruisesAction()
    {
        $em         = $this->getDoctrine()->getManager();
        $footerLinks= $em->getRepository('GushhCoreBundle:StaticPage')->findBy(['enabled' => true]);

        return [
            'activePage' => 'cruises',
            'footerLinks'=> $footerLinks,
        ];
    }

    /**
     * @Route("/static/{slug}/", name="static_page")
     * @Template()
     */
    public function staticPageAction($slug)
    {
        $em          = $this->getDoctrine()->getManager();

        $page        = $this->get('request')->getLocale() == 'es'
                        ? $em->getRepository('GushhCoreBundle:StaticPage')->findOneByEsSlug($slug)
                        : $em->getRepository('GushhCoreBundle:StaticPage')->findOneByEnSlug($slug);
        $footerLinks = $em->getRepository('GushhCoreBundle:StaticPage')->findBy(['enabled' => true]);

        return [
            'entity'     => $page,
            'activePage' => 'static',
            'footerLinks'=> $footerLinks,
        ];
    }
}
