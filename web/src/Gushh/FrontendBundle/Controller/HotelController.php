<?php

namespace Gushh\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Gushh\CoreBundle\Entity\Search;
use Gushh\CoreBundle\Form\SearchType;

use Gushh\CoreBundle\Classes\Util;

class HotelController extends Controller
{

    /**
     * @Route("/hotels/", name="hotels")
     * @Template()
     */
    public function indexAction()
    {
        $em     = $this->getDoctrine()->getManager();

        $footerLinks= $em->getRepository('GushhCoreBundle:StaticPage')->findBy(['enabled' => true]);

        $usa    = $em->getRepository('GushhCoreBundle:Hotel')->findStates('america', 'United States', 5, false); // Set limit 5
        $mexico    = $em->getRepository('GushhCoreBundle:Hotel')->findStates('america', 'mexico', 5, false); // Set limit 5
        $europe = $em->getRepository('GushhCoreBundle:Hotel')->findStates('europe', null, 5, false); // Set limit 5
        $argentina    = $em->getRepository('GushhCoreBundle:Hotel')->findStates('america', 'argentina', 5, false); // Set limit 5
        
        $states = [
            'usa'       => $usa,
            'europe'    => $europe,
            'mexico'    => $mexico,
            'argentina'    => $argentina
        ];

        return [
            'activePage'    => 'hotels',
            'states'        => $states,
            'footerLinks'=> $footerLinks,
        ];
    }

    /**
     * @Route("/hotels/{region}/{continent}/", requirements={"continent" = "america|europe"}, name="hotels_list_continent")
     * @Template()
     */
    public function continentBlockAction($region, $continent)
    {
        $continent  = str_replace('-', ' ', $continent);

        if ($continent == 'america' && $region == 'United States') {
            return $this->redirect($this->generateUrl('hotels_list_country', ['region' => $region, 'continent' => 'america', 'country' => 'united-states']));
        }

        $em = $this->getDoctrine()->getManager();

        $isVip = $this->getUser() != null && $this->getUser()->getVip();
        $entities = ($continent == 'america') ? $em->getRepository('GushhCoreBundle:Hotel')->findStates($continent, $region, null, $isVip)
                                                : $em->getRepository('GushhCoreBundle:Hotel')->findStates($continent, null, null, $isVip);

        $footerLinks= $em->getRepository('GushhCoreBundle:StaticPage')->findBy(['enabled' => true]);

        return [
            'activePage'    => 'hotels',
            'continent'     => $continent,
            'entities'      => $entities,
            'footerLinks'=> $footerLinks,
        ];
    }

    /**
     * @Route("/hotels/{region}/{continent}/{country}/", requirements={"continent" = "america|europe"}, name="hotels_list_country")
     * @Template()
     */
    public function countryBlockAction($region, $continent, $country)
    {
        $continent  = str_replace('-', ' ', $continent);
        $country    = str_replace('-', ' ', $country);

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('GushhCoreBundle:Hotel')->findStates($continent, $country, null, false);

        $footerLinks= $em->getRepository('GushhCoreBundle:StaticPage')->findBy(['enabled' => true]);

        if ($country == 'united states') {
            $country = 'Usa';
        }

        return [
            'activePage'    => 'hotels',
            'region'        => $region,
            'continent'     => $continent,
            'country'       => $country,
            'entities'      => $entities,
            'footerLinks'   => $footerLinks,
        ];
    }    

    /**
     * @Route("/hotels/{region}/{continent}/{country}/{city}/", requirements={"continent" = "america|europe"}, name="hotels_list_city")
     * @Template()
     */
    public function cityListAction($region, $continent, $country, $city)
    {
        $continent  = str_replace('-', ' ', $continent);
        $country    = str_replace('-', ' ', $country);
        $city       = str_replace('-', ' ', $city);

        $em = $this->getDoctrine()->getManager();
        $hotels = $em->getRepository('GushhCoreBundle:Hotel')->findBy([
            'enabled'   => true, 
            'continent' => $continent,
            'country'   => $country,
            'city'      => $city,
        ]);

        $footerLinks= $em->getRepository('GushhCoreBundle:StaticPage')->findBy(['enabled' => true]);

        if (!$hotels) {
            throw $this->createNotFoundException("No records: city = '$city'");
        }

        return [
            'activePage'    => 'hotels',
            'hotels'        => $hotels,
            'footerLinks'   => $footerLinks,
        ];
    }

    /**
     * @Route("/hotels/{continent}/{country}/{city}/{hotel}/", requirements={"continent" = "america|europe"}, name="hotel_detail")
     * @Template()
     */
    public function detailsAction($continent, $country, $city, $hotel)
    {
        $continent  = str_replace('-', ' ', $continent);
        $country    = str_replace('-', ' ', $country);
        $city       = str_replace('-', ' ', $city);

        $em = $this->getDoctrine()->getManager();
        $hotel = $em->getRepository('GushhCoreBundle:Hotel')->findOneBy([
            'enabled'   => true, 
            'continent' => $continent,
            'country'   => $country,
            'city'      => $city, 
            'slug'      => $hotel
        ]);

        $footerLinks= $em->getRepository('GushhCoreBundle:StaticPage')->findBy(['enabled' => true]);

        if (!$hotel) {
            throw $this->createNotFoundException("No records: city = '$city', hotel = '$slug'");
        }
        
        return [
            'activePage'    => 'hotels',
            'hotel'         => $hotel,
            'footerLinks'   => $footerLinks,
        ];
    }

}
