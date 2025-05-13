<?php

namespace Gushh\BackendBundle\Controller;

use Gushh\CoreBundle\Classes\MailCore;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Gushh\CoreBundle\Entity\CheckOut;
use Gushh\CoreBundle\Form\CheckOutType;
use Gushh\CoreBundle\Form\CheckOutConfirmationType;

use Gushh\CoreBundle\Entity\Reservation;
use Gushh\CoreBundle\Entity\ReservationDate;

use Gushh\CoreBundle\Classes\Util;
use Gushh\CoreBundle\Classes\SearchCore;

/**
 * CheckOut controller.
 *
 * @Route("/dashboard/client/checkout")
 */
class CheckOutController extends Controller
{

    /**
     * Displays a form to create a new CheckOut entity.
     *
     * @Route("/", name="backend_checkout")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $asd = SearchCore::isOutsideCutOff(3);

        var_dump($asd);
        die();

        return [];
    }

    /**
     * Creates a new CheckOut entity.
     *
     * @Route("/{scode}-{hid}-{rid}-{promo}/step-one/-/", name="backend_checkout_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:CheckOut:stepOne.html.twig")
     */
    public function createAction(Request $request, $scode, $hid, $rid, $promo)
    {

        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $search = $em->getRepository('GushhCoreBundle:Search')->findOneBy(['code' => $scode, 'user' => $user]);
        $hotel = $em->getRepository('GushhCoreBundle:Hotel')->findOneById($hid);
        $room = $em->getRepository('GushhCoreBundle:Room')->findOneById($rid);


        $location = [
            'continent' => str_replace(" ", "-", strtolower($hotel->getContinent())),
            'country' => str_replace(" ", "-", strtolower($hotel->getCountry())),
            'city' => str_replace(" ", "-", strtolower($hotel->getCity())),
        ];

        $data = [
            'hid'   => $hid,
            'rid'   => $rid,
            'scode' => $scode,
            'promo' => $promo,
        ];

        $entity = new CheckOut();
        $form = $this->createCreateForm($entity, $scode, $hid, $rid, $promo);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($form->isValid()) {
            $entity->setSearch($search);
            $entity->setUser($user);
            $agency = $user->getAgency();
            foreach ($entity->getGuests() as $guest)
                $guest->setAgency($agency);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_checkout_confirmation', ['coid' => $entity->getId(), 'scode' => $scode, 'hid' => $hid, 'rid' => $rid, 'promo' => $promo]));
        }

        return [
            'hotel'       => $hotel,
            'room'        => $room,
            'location'    => $location,
            'search'      => $search,            
            'data'        => $data,            
            'entity'      => $entity,
            'form'        => $form->createView(),
        ];
    }

    /**
     * Creates a new CheckOut entity.
     *
     * @Route("/{scode}-{hid}-{rid}-{promo}/step-one/", defaults={"promo"=0}, name="backend_checkout_step_one")
     * @Template()
     */
    public function stepOneAction(Request $request, $scode, $hid, $rid, $promo)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $search = $em->getRepository('GushhCoreBundle:Search')->findOneBy(['code' => $scode, 'user' => $user]); 
        $checkout = $em->getRepository('GushhCoreBundle:CheckOut')->findOneBy(['search' => $search]);

        if ($checkout) {
            return $this->redirect($this->generateUrl('client_dashboard'));
        }

        $entity = new CheckOut();
        $form = $this->createCreateForm($entity, $scode, $hid, $rid, $promo);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setUser($user);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_checkout_confirmation', ['coid' => $entity->getId(), 'scode' => $scode, 'hid' => $hid, 'rid' => $rid, 'promo' => $promo]));
        }

        $hotel = $em->getRepository('GushhCoreBundle:Hotel')->findOneById($hid);
        $room = $em->getRepository('GushhCoreBundle:Room')->findOneById($rid);

        $searchData = SearchCore::getSearchData($search, $user, $em, $hotel, $room, $promo);

        $location = [
            'continent' => str_replace(" ", "-", strtolower($hotel->getContinent())),
            'country'   => str_replace(" ", "-", strtolower($hotel->getCountry())),
            'city'      => str_replace(" ", "-", strtolower($hotel->getCity())),
        ];

        $data = [
                'hid'   => $hid,
                'rid'   => $rid,
                'scode' => $scode,
                'promo' => $promo,
                'cancellationPolicy' => $hotel->getPolicies(),
                'currency' => $hotel->getCurrency()->getSymbol(),
                'price' => $searchData['totalWithMandatoryServices'],
                'isBlackedOut' => $searchData['isBlackedOut'],
                'isCancellable' => $searchData['isCancellable'],
                'cancellableUntil' => $searchData['cancellableUntil'],
                'cancellationPrice' => $searchData['cancellationPrice'],
                'adults' => $searchData['adults'],
                'children' => $searchData['children'],
                'pax' => ($searchData['adults'] + $searchData['children'])
            ];

        return [
            'hotel'       => $hotel,
            'room'        => $room,
            'location'    => $location,
            'search'      => $search,
            'data'        => $data,
            'entity'      => $entity,
            'form'        => $form->createView(),
        ];
    }

    /**
     * Creates a form to create a CheckOut entity.
     *
     * @param CheckOut $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CheckOut $entity, $scode, $hid, $rid, $promo)
    {
        $form = $this->createForm(new CheckOutType(), $entity, array(
            'action' => $this->generateUrl('backend_checkout_create', ['scode' => $scode, 'hid' => $hid, 'rid' => $rid, 'promo' => $promo]),
            'attr' => array(
                'id'    => 'checkOutForm',
                'autocomplete' => 'off'
                ),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to edit an existing CheckOut entity.
     *
     * @Route("/{coid}-{scode}-{hid}-{rid}-{promo}/confirmation/", defaults={"promo"=0}, name="backend_checkout_confirmation")
     * @Template()
     */
    public function confirmationAction(Request $request, $coid, $scode, $hid, $rid, $promo)
    {
        $user = $this->getUser();

        $em         = $this->getDoctrine()->getManager();
        $checkout   = $em->getRepository('GushhCoreBundle:CheckOut')->findOneById($coid);

        if ($checkout->getConfirmed()) {
            return $this->redirect($this->generateUrl('client_dashboard'));
        }

        $search     = $em->getRepository('GushhCoreBundle:Search')->findOneBy(['code' => $scode, 'user' => $user]);
        $hotel      = $em->getRepository('GushhCoreBundle:Hotel')->findOneById($hid);
        $room       = $em->getRepository('GushhCoreBundle:Room')->findOneById($rid);

        $searchData = SearchCore::getSearchData($search, $user, $em, $hotel, $room, $promo);
        $editForm = $this->createEditForm($checkout, 'CONFIRMATION', $scode, $hid, $rid, $promo);

        if ($editForm->isValid()) {
            $em->persist($checkout);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_checkout'));
        }

        $location = [
            'continent' => str_replace(" ", "-", strtolower($hotel->getContinent())),
            'country'   => str_replace(" ", "-", strtolower($hotel->getCountry())),
            'city'      => str_replace(" ", "-", strtolower($hotel->getCity())),
        ];

        $data = [
            'hid'   => $hid,
            'rid'   => $rid,
            'scode' => $scode,
            'promo' => $promo,
            'adults' => $searchData['adults'],
            'children' => $searchData['children'],
                ];


        return [
            'reservation' => $searchData,
            'checkout'    => $checkout,
            'hotel'       => $hotel,
            'room'        => $room,
            'location'    => $location,
            'search'      => $search,  
            'data'        => $data,
            'entity'      => $checkout,
            'currency'    => $hotel->getCurrency()->getSymbol(),
            'form'        => $editForm->createView(),
            // 'deleteForm' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing CheckOut entity.
     *
     * @Route("/{coid}-{scode}-{hid}-{rid}-{promo}/step-one/u/", defaults={"promo"=0}, name="backend_checkout_step_one_edit")
     * @Template("GushhBackendBundle:CheckOut:stepOne.html.twig")
     */
    public function editStepOneAction(Request $request, $coid, $scode, $hid, $rid, $promo)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('GushhCoreBundle:CheckOut')->find($coid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CheckOut entity.');
        }

        $editForm = $this->createEditForm($entity, 'STEP-ONE', $scode, $hid, $rid, $promo);
        // $deleteForm = $this->createDeleteForm($coid);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_checkout_confirmation', ['coid' => $coid, 'scode' => $scode, 'hid' => $hid, 'rid' => $rid, 'promo' => $promo]));
        }

        $user = $this->getUser();
        $search = $em->getRepository('GushhCoreBundle:Search')->findOneBy(['code' => $scode, 'user' => $user]);
        $hotel = $em->getRepository('GushhCoreBundle:Hotel')->findOneById($hid);
        $room = $em->getRepository('GushhCoreBundle:Room')->findOneById($rid);
        $searchData = SearchCore::getSearchData($search, $user, $em, $hotel, $room, $promo);

        $location = [
            'continent' => str_replace(" ", "-", strtolower($hotel->getContinent())),
            'country' => str_replace(" ", "-", strtolower($hotel->getCountry())),
            'city' => str_replace(" ", "-", strtolower($hotel->getCity())),
        ];

        $data = [
            'hid'   => $hid,
            'rid'   => $rid,
            'scode' => $scode,
            'promo' => $promo,
            'cancellationPolicy' => $hotel->getPolicies(),
            'currency' => $hotel->getCurrency()->getSymbol(),
            'price' => $searchData['totalWithMandatoryServices'],
            'isBlackedOut' => $searchData['isBlackedOut'],
            'isCancellable' => $searchData['isCancellable'],
            'cancellableUntil' => $searchData['cancellableUntil'],
            'cancellationPrice' => $searchData['cancellationPrice'],
            'adults' => $searchData['adults'],
            'children' => $searchData['children'],
        ];

        return [
            'hotel'       => $hotel,
            'room'        => $room,
            'location'    => $location,
            'search'      => $search,  
            'data'        => $data,  
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            // 'deleteForm' => $deleteForm->createView(),
        ];
    }

    /**
    * Creates a form to edit a CheckOut entity.
    *
    * @param CheckOut $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CheckOut $entity, $type, $scode, $hid, $rid, $promo)
    {
        if ($type === 'CONFIRMATION') {
            $form = $this->createForm(new CheckOutConfirmationType(), $entity, array(
                'action' => $this->generateUrl('backend_checkout_confirmation_update', ['coid' => $entity->getId(), 'hid' => $hid, 'rid' => $rid, 'scode' => $scode, 'promo' => $promo]),
                'attr' => array(
                    'id'    => 'checkOutForm',
                    'autocomplete' => 'off'
                    ),
                'method' => 'PUT',
            ));
        } elseif ($type === 'STEP-ONE') {
            $form = $this->createForm(new CheckOutType(), $entity, array(
                'action' => $this->generateUrl('backend_checkout_step_one_update', ['coid' => $entity->getId(), 'hid' => $hid, 'rid' => $rid, 'scode' => $scode, 'promo' => $promo]),
                'attr' => array(
                    'id'    => 'checkOutForm',
                    'autocomplete' => 'off'
                    ),
                'method' => 'PUT',
            ));
        }

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    /**
     * Edits an existing CheckOut entity.
     *
     * @Route("/{coid}-{scode}-{hid}-{rid}-{promo}/step-one/ua/", defaults={"promo"=0}, name="backend_checkout_step_one_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:CheckOut:editStepOne.html.twig")
     */
    public function updateStepOneAction(Request $request, $coid, $scode, $hid, $rid, $promo)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $search = $em->getRepository('GushhCoreBundle:Search')->findOneBy(['code' => $scode, 'user' => $user]);
        $hotel = $em->getRepository('GushhCoreBundle:Hotel')->findOneById($hid);
        $room = $em->getRepository('GushhCoreBundle:Room')->findOneById($rid);


        $location = [
            'continent' => str_replace(" ", "-", strtolower($hotel->getContinent())),
            'country' => str_replace(" ", "-", strtolower($hotel->getCountry())),
            'city' => str_replace(" ", "-", strtolower($hotel->getCity())),
        ];

        $data = [
            'hid'   => $hid,
            'rid'   => $rid,
            'scode' => $scode,
            'promo' => $promo,
        ];

        $entity = $em->getRepository('GushhCoreBundle:CheckOut')->find($coid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CheckOut entity.');
        }

        $editForm = $this->createEditForm($entity, 'STEP-ONE', $scode, $hid, $rid, $promo);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_checkout_confirmation', ['coid' => $coid, 'scode' => $scode, 'hid' => $hid, 'rid' => $rid, 'promo' => $promo]));
        }

        return [
            'hotel'       => $hotel,
            'room'        => $room,
            'search'      => $search,
            'location'    => $location,
            'data'        => $data,
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            // 'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Edits an existing CheckOut entity.
     *
     * @Route("/{coid}-{scode}-{hid}-{rid}-{promo}/confirmation/u/", defaults={"promo"=0}, name="backend_checkout_confirmation_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:CheckOut:confirmation.html.twig")
     */
    public function updateConfirmationAction(Request $request, $coid, $scode, $hid, $rid, $promo)
    {
        $em         = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:CheckOut')->find($coid);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CheckOut entity.');
        }

        $user = $this->getUser();
        $search     = $em->getRepository('GushhCoreBundle:Search')->findOneBy(['code' => $scode, 'user' => $user]);
        $hotel      = $em->getRepository('GushhCoreBundle:Hotel')->findOneById($hid);
        $room       = $em->getRepository('GushhCoreBundle:Room')->findOneById($rid);
        $checkout   = $em->getRepository('GushhCoreBundle:CheckOut')->findOneById($coid);

        $searchData = SearchCore::getSearchData($search, $user, $em, $hotel, $room, $promo);

        // $deleteForm = $this->createDeleteForm($coid);
        $editForm = $this->createEditForm($entity, 'CONFIRMATION', $scode, $hid, $rid, $promo);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {


            $subtotal   = $searchData['total'];
            $commission = $searchData['commission'];
            $services   = $searchData['mandatoryServicesTotal'];
            $total      = $searchData['totalWithMandatoryServices'];

            if ($searchData['onRequest'] == false && $searchData['isBlackedOut'] == false)
                $statusName = 'completed';
            else
                $statusName = 'on-request';

            $this->substractStock($searchData['stayDates']);
            $status = $em->getRepository('GushhCoreBundle:ReservationStatus')->findOneByCode($statusName);
            $paymentStatus = $em->getRepository('GushhCoreBundle:ReservationPaymentStatus')->findOneByCode('unpaid');

            $reservation = new Reservation();
            $reservation->setCode(' ');
            $reservation->setOperator($user);
            $reservation->setStatus($status);
            $reservation->setPaymentStatus($paymentStatus);
            $reservation->setSubtotal($subtotal);
            $reservation->setCommission($commission);
            $reservation->setServicesPrice($services);
            $reservation->setTotal($total);

            $reservation->setPriceNet($searchData['priceNet']);
            $reservation->setTotalNet($searchData['totalWithServicesNet']);
            $reservation->setTaxesNet($searchData['taxesNet']);


            $reservation->setRoom($room);
            $reservation->setHotel($hotel);
            $reservation->setCancellableUntil($searchData['cancellableUntil']);


            $reservation->setAdults($searchData['adults']);
            $reservation->setChildren($searchData['children']);
            $reservation->setIsBlackedOut($searchData['isBlackedOut']);
            $reservation->getIsOnRequest($searchData['onRequest']);
            $reservation->setIsCancellable($searchData['isCancellable']);
            if ( isset($searchData['isPromotion']) == true && $searchData['promotion'] == true ) {
                $reservation->setPromotionsNames($searchData['promotion']);
            }
            else {
                $reservation->setPromotionsNames('');
            }


            $em->persist($reservation);
            $em->persist($entity);
            $em->flush();

            $reservation->setCode($reservation->getId());

            foreach ($searchData['stayDates'] as $stayDate) {

                $date = new ReservationDate();
                $date->setReservation($reservation);
                $date->setDate($stayDate['date']);
                $date->setNightTax($stayDate['nightTax']);
                $date->setNightOccupancyTax($stayDate['nightOccupancyTax']);
                $date->setPriceNetRemaining($stayDate['priceNetRemaining']);
                $date->setPriceNet($stayDate['priceNet']);
                $date->setMandatoryServices($stayDate['mandatoryServices']);
                $date->setMandatoryServicesRemaining($stayDate['mandatoryServicesRemaining']);
                $date->setProfit($stayDate['profit']);
                $date->setCancellationPenalty($stayDate['cancellationPenalty']);
                $date->setTotalNet($stayDate['totalNet']);
                $date->setTotalWithServicesNet($stayDate['totalWithServicesNet']);
                $date->setPrice($stayDate['price']);
                $date->setTax($stayDate['tax']);
                $date->setCommission($stayDate['commission']);
                $date->setTotal($stayDate['total']);
                $date->setTotalWithServices($stayDate['totalWithServices']);
                $date->setTotalWithServicesAndCommission($stayDate['totalWithServicesAndCommission']);

                $date->setIsBlackedOut($stayDate['isBlackedOut']);
                $date->setIsPremium($stayDate['isPremium']);
                $date->setOnRequest($stayDate['onRequest']);
                $date->setIsPromotion($stayDate['isPromotion']);
                $date->setIsFree($stayDate['isFree']);
                $date->setDateWithDiscount($stayDate['dateWithDiscount']);
                $em->persist($date);
                $reservation->addReservationDate($date);
            }

            $entity->setReservation($reservation);
            $em->persist($reservation);
            $em->persist($entity);
            $em->flush();

            $mailCore = new MailCore($this->get('mailer'), 'requestHotel',
                                ['twig' => $this->get('twig'),
                                 'pdf' => $this->get('knp_snappy.pdf'),
                                 'reservation' => $reservation,
                                 'checkOut' => $checkout]);

            $mailCore->sendEmail();
            return $this->redirect($this->generateUrl('client_reservations'));
        }

        $location = [
            'continent' => str_replace(" ", "-", strtolower($hotel->getContinent())),
            'country'   => str_replace(" ", "-", strtolower($hotel->getCountry())),
            'city'      => str_replace(" ", "-", strtolower($hotel->getCity())),
        ];

        $data = [
            'hid'   => $hid,
            'rid'   => $rid,
            'scode' => $scode,
            'promo' => $promo,
        ];

        return [
            'reservation' => $searchData,
            'checkout'    => $checkout,
            'hotel'       => $hotel,
            'room'        => $room,
            'location'    => $location,
            'search'      => $search,  
            'data'        => $data,  
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            // 'delete_form' => $deleteForm->createView(),
        ];
    }

    public function substractStock($stayDates)
    {
        foreach ($stayDates as $stayDate) {
            $date = $stayDate['date'];
            $stock = $date->getStock() -1;
//            if($stock < 0)
//                $stock = 0;

            $date->setStock($stock);
            // Don't need to call persist here since it is going to be called in the calling function
        }
    }
}
