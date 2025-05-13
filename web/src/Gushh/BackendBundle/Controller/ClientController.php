<?php

namespace Gushh\BackendBundle\Controller;

use Gushh\CoreBundle\Entity\Reservation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Agency controller.
 *
 * @Route("/dashboard/client")
 */
class ClientController extends Controller
{

  /**
   * Lists all Agency entities.
   *
   * @Route("/", name="client_dashboard")
   * @Method("GET")
   * @Template()
   */
  public function indexAction()
  {

    $em = $this->getDoctrine()->getManager();

    $user         = $this->getUser();
    $agency       = $user->getAgency();
    $usaHotels    = count($em->getRepository('GushhCoreBundle:Hotel')->findStates('america', 'United States', null, $user->getVip()));
    $europeHotels = count($em->getRepository('GushhCoreBundle:Hotel')->findStates('europe', null, null, $user->getVip()));
    $rooms        = count($em->getRepository('GushhCoreBundle:Room')->findAll());
    $reservations = count($em->getRepository('GushhCoreBundle:Reservation')->findByAgency($agency));
    $promotions   = count($em->getRepository('GushhCoreBundle:Promotion')->findAll());
    $searches     = count($em->getRepository('GushhCoreBundle:Search')->findBy(['user' => $user]));
    
    $stats = [
      'searches'      => $searches,
      'reservations'  => $reservations,
      'promotions'    => $promotions,
      'usaHotels'     => $usaHotels,
      'europeHotels'  => $europeHotels,
      'rooms'         => $rooms,
    ];


    return [
      'stats' => $stats
    ];

  }

  /**
   * Lists all Reservation entities.
   *
   * @Route("/reservations/", name="client_reservations")
   * @Method("GET")
   * @Template()
   */
  public function reservationAction()
  {
    $em = $this->getDoctrine()->getManager();

    $agency = $this->getUser()->getAgency();

    $entities = $em->getRepository('GushhCoreBundle:Reservation')->findByAgency($agency);

    return [
      'entities' => $entities,
    ];
  }

  /**
   * Search form
   *
   * @Route("/search/hotels/", name="client_search")
   * @Method("GET")
   * @Template()
   */
  public function searchAction()
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

    /**
     * Displays a form to create a new entity.
     *
     * @Route("/checkout/{scode}-{hid}-{rid}-{promo}/step-one/", name="backend_room_rate_new")
     * @Method("GET")
     * @Template()
     */
//    public function checkoutAction($scode, $hid, $rid, $pid)
//    {
//        $entity = new Reservation();
//        $form   = $this->createCreateForm($entity, $hid, $rid);
//
//        $em = $this->getDoctrine()->getManager();
//        $room = $em->getRepository('GushhCoreBundle:Room')->find($rid);
//
//        return array(
//            'room'  => $room,
//            'entity' => $entity,
//            'form'   => $form->createView(),
//        );
//    }
}
