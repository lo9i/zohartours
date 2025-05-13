<?php

namespace Gushh\BackendBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\ReservationPayment;
use Gushh\CoreBundle\Form\ReservationPaymentType;

/**
 * Reservation controller.
 *
 * @Route("/dashboard/reservation")
 */
class ReservationPaymentController extends Controller
{

//    /**
//     * Lists all Payments entities for .
//     *
//     * @Route("s/", name="backend_payments")
//     * @Method("GET")
//     * @Template()
//     */
//    public function indexAction()
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entities = $em->getRepository('GushhCoreBundle:ReservationPayment')->findAll();
//
//        return array(
//            'entities' => $entities,
//        );
//    }
//
    /**
     * Displays a form to create a new Promotion entity.
     *
     * @Route("/{rid}/payment/new/", name="backend_reservation_payment_new")
     * @Method("GET")
     * @Template("GushhBackendBundle:ReservationPayment:new.html.twig")
     */
    public function newReservationPaymentAction($rid)
    {
        $entity = new ReservationPayment();

        $em = $this->getDoctrine()->getManager();
        $reservation = $em->getRepository('GushhCoreBundle:Reservation')->findOneById($rid);

        $form   = $this->createCreateForm($entity, $reservation);


        return [
            'entity'      => $entity,
            'reservation' => $reservation,
            'form'        => $form->createView(),
            'currency'   => $reservation->getHotel()->getCurrency()->getSymbol()
        ];
    }

    /**
     * Creates a new Reservation Payment entity.
     *
     * @Route("/{rid}/payment/", name="backend_reservation_payment_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:ReservationPayment:new.html.twig")
     */
    public function createPaymentAction(Request $request, $rid)
    {
        $entity = new ReservationPayment();
        $em = $this->getDoctrine()->getManager();
        $reservation = $em->getRepository('GushhCoreBundle:Reservation')->findOneById($rid);

        $form = $this->createCreateForm($entity,$reservation);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $balance = round(round($reservation->getBalance(),2) - round($entity->getAmount(), 2),2);
            if($balance < 0) {
                $form->get('amount')->addError(new FormError('Balance must be positive. Current: ' . $reservation->getBalance()));
                return [
                    'entity'    => $entity,
                    'reservation' => $reservation,
                    'form'      => $form->createView(),
                    'currency'   => $reservation->getHotel()->getCurrency()->getSymbol()
                ];
            }

            $entity->setReservation($reservation);
            $entity->setUser($this->getUser());

            $em->persist($entity);
            $newStatus = null;
            if ( $balance == 0 ) {
                $newStatus = $em->getRepository('GushhCoreBundle:ReservationPaymentStatus')->findOneBy(array('code' => 'paid'));
            } else if( $reservation->getPaymentStatus()->getCode() != 'partial') {
                $newStatus = $em->getRepository('GushhCoreBundle:ReservationPaymentStatus')->findOneBy(array('code' => 'partial'));
            }

            if($newStatus != null) {
                $reservation->setPaymentStatus($newStatus);
                $em->persist($reservation);
            }
            $em->flush();
            return $this->redirect($this->generateUrl('backend_reservation_show', ['id' => $rid] ));
        }

        return [
            'entity'    => $entity,
            'reservation' => $reservation,
            'form'      => $form->createView(),
            'currency'   => $reservation->getHotel()->getCurrency()->getSymbol()
        ];
    }

    /**
     * Finds and displays a Reservation entity.
     *
     * @Route("/{rid}/payment/{pid}", name="backend_reservation_payment_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($rid, $pid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:ReservationPayment')->find($pid);
        $reservation = $em->getRepository('GushhCoreBundle:Reservation')->find($rid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reservation payment entity.');
        }

        return array(
            'entity'      => $entity,
            'reservation' => $reservation,
            'currency'   => $reservation->getHotel()->getCurrency()->getSymbol()
        );
    }

    /**
     * Creates a form to create a Promotion entity.
     *
     * @param ReservationPayment $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ReservationPayment $entity, $reservation)
    {
        return $this->createForm(new ReservationPaymentType(), $entity, array(
            'action' => $this->generateUrl('backend_reservation_payment_create', ['rid' => $reservation->getId()]),
            'attr' => array(
                'id' => 'reservationPaymentForm'
            ),
            'currency' => $reservation->getHotel()->getCurrency()->getShortName(),
            'method' => 'POST'));
    }
}
