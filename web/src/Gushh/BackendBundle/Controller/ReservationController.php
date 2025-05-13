<?php

namespace Gushh\BackendBundle\Controller;
use Gushh\CoreBundle\Classes\MailCore;
use Gushh\CoreBundle\Entity\ReservationDate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\Reservation;
use Gushh\CoreBundle\Entity\Invoice;
use Gushh\CoreBundle\Entity\InvoiceItem;
use Gushh\CoreBundle\Form\ReservationType;
use Gushh\CoreBundle\Classes\Util;
use Carbon\Carbon;
use Numbers_Words;
/**
 * Reservation controller.
 *
 * @Route("/dashboard/reservation")
 */
class ReservationController extends Controller
{

    /**
     * Lists all Reservation entities.
     *
     * @Route("s/", name="backend_reservations")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GushhCoreBundle:Reservation')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Reservation entity.
     *
     * @Route("/{id}/", name="backend_reservation_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Reservation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reservation entity.');
        }

        $isFuture = Carbon::instance($entity->getCancellableUntil());
        $canceled = $entity->getStatus()->getCode() == "cancelled";
        $paid     = $entity->getPaymentStatus()->getCode() == "paid";

        $modifiable = $isFuture->isFuture() && $canceled == false;

        $stayDates = [];
        foreach ($entity->getReservationDates() as $stayDate)
            $stayDates[] = $this->buildStayDate($stayDate);

        return array(
            'entity'     => $entity,
            'modifiable' => $modifiable,
            'paymentsAmount' => $entity->getPaymentsAmount(),
            'currency'   => $entity->getHotel()->getCurrency()->getSymbol(),
            'canceled'   => $canceled,
            'paid'       => $paid,
            'calendar'  => Util::makeCalendar($stayDates, $entity->getCheckOutProcess()->getSearch()->getNights(), false),
        );
    }

     /**
      * Displays a form to edit an existing Reservation entity.
      *
      * @Route("/{id}/edit", name="backend_reservation_edit")
      * @Method("GET")
      * @Template()
      */
     public function editAction($id)
     {
         $em = $this->getDoctrine()->getManager();

         $entity = $em->getRepository('GushhCoreBundle:Reservation')->find($id);

         if (!$entity) {
             throw $this->createNotFoundException('Unable to find Reservation entity.');
         }

         $editForm = $this->createEditForm($entity);

         return array(
             'entity'      => $entity,
             'form'   => $editForm->createView(),
             'currency'   => $entity->getHotel()->getCurrency()->getSymbol()
         );
     }

    /**
    * Creates a form to edit a Reservation entity.
    *
    * @param Reservation $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Reservation $entity)
    {
        $form = $this->createForm(new ReservationType(), $entity, array(
            'action' => $this->generateUrl('backend_reservation_update', array('id' => $entity->getId())),
            'attr' => array(
                'id'    => 'reservationForm',
                'autocomplete' => 'off'
                ),
            'method' => 'PUT',
            'role' => $this->getUser()->getRoles()
        ));

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

     /**
      * Edits an existing Reservation entity.
      *
      * @Route("/{id}", name="backend_reservation_update")
      * @Method("PUT")
      * @Template("GushhBackendBundle:Reservation:edit.html.twig")
      */
     public function updateAction(Request $request, $id)
     {
         $em = $this->getDoctrine()->getManager();

         $entity = $em->getRepository('GushhCoreBundle:Reservation')->find($id);

         if (!$entity) {
             throw $this->createNotFoundException('Unable to find Reservation entity.');
         }

         // Keep the original status so we can compare after

         $editForm = $this->createEditForm($entity);
         $editForm->handleRequest($request);

         if ($editForm->isValid()) {

             // Hack to avoid editing this on entities before dateReservation was added
            if($entity->getId() > 1224) {
                $total = 0;
                $totalNet = 0;
                foreach ($entity->getReservationDates() as $stayDate) {
                    $total += $stayDate->getTotalWithServicesAndCommission();
                    $totalNet += $stayDate->getTotalWithServicesNet();

                }

                if ($total != $entity->getTotal())
                    $entity->setTotal($total);

                if ($totalNet != $entity->getTotalNet())
                    $entity->setTotalNet($totalNet);
            }

            $newStatusName = $entity->getStatus()->getName();
            $em->flush();

             if($newStatusName == "Cancelled") {

                 $checkout = $entity->getCheckOutProcess();
                 $room = $checkout->getReservation()->getRoom();
                 if ($room->getContractType()->getSlug() != 'on-request' ) {
                     $days = Util::createRange($checkout->getSearch()->getCheckIn(), $checkout->getSearch()->getCheckOut(), false);
                     $datesEntities = $em->getRepository('GushhCoreBundle:Date')->findDatesByRoomAndDays($room->getId(), $days, $checkout->getSearch()->getNights());
                     if ($datesEntities) {
                         foreach ($datesEntities as $date) {
                             $stock = $date->getStock();
                             $stock += 1;
                             $date->setStock($stock);
                             $em->persist($date);
                             // Don't need to call persist here since it is going to be called in the calling function
                         }
                         $em->flush();
                     }
                 }

                 $mailCore = new MailCore($this->get('mailer'), 'requestHotelCancelled', ['twig' => $this->get('twig'), 'pdf' => $this->get('knp_snappy.pdf'),
                     'reservation' => $entity, 'checkOut' => $checkout]);
                 $mailCore->sendEmail();
             }

             return $this->redirect($this->generateUrl('backend_reservation_show', array('id' => $entity->getId()) ));
         }

         return array(
             'entity'      => $entity,
             'edit_form'   => $editForm->createView(),
         );
     }

    /**
     * Edits an existing CheckOut entity.
     *
     * @Route("/resend/{coid}/", name="backend_reservation_resend_confirmation")
     * @Method("POST")
     */
    public function sendReservationEmailAction($coid)
    {
        $em         = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:CheckOut')->find($coid);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CheckOut entity.');
        }


        $mailCore = new MailCore($this->get('mailer'), 'requestHotel',
                                ['twig' => $this->get('twig'),
                                 'pdf' => $this->get('knp_snappy.pdf'),
                                 'reservation' => $entity->getReservation(),
                                 'checkOut' => $entity, 'user' => $entity->getUser()]);
        $mailCore->sendEmail();
        return new Response('Success');
    }

    /**
     * Edits an existing CheckOut entity.
     *
     * @Route("/resend-cancel/{coid}/", name="backend_reservation_resend_cancel")
     * @Method("POST")
     */
    public function sendReservationCancelEmailAction($coid)
    {
        $em         = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:CheckOut')->find($coid);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CheckOut entity.');
        }

        $mailCore = new MailCore($this->get('mailer'), 'requestHotelCancelled', ['twig' => $this->get('twig'), 'pdf' => $this->get('knp_snappy.pdf'),
            'reservation' => $entity->getReservation(), 'checkOut' => $entity]);

        $mailCore->sendEmail();
        return new Response('Success');
    }

    /**
     * Build Reservation Voucher
     *
     * @Route("/voucher/{coid}/", name="backend_reservation_voucher")
     * @Method("GET")
     */
    public function voucherAction($coid)
    {
        $em         = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:CheckOut')->find($coid);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CheckOut entity.');
        }

        $pdfBuilder = $this->get('knp_snappy.pdf');
        $reservation = $entity->getReservation();
        $checkOut = $entity;

        $html = $this->renderView('GushhBackendBundle:Vouchers:hotelReservation.html.twig',
            [
                'reservation' => $reservation,
                'checkout'  => $checkOut,
            ]);

        $filename = $reservation->getCode() . '.pdf';
        $response = new Response($pdfBuilder->getOutputFromHtml($html));
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename
        );

        return $response;
    }

    /**
     * Creates a new Invoice entity for a reservation.
     *
     * @Route("/invoice/{rid}/", name="backend_reservation_invoice")
     * @Method("GET")
     */
    public function invoiceAction($rid)
    {
        $em = $this->getDoctrine()->getManager();

        $reservation = $em->getRepository('GushhCoreBundle:Reservation')->find($rid);

        if (!$reservation) {
            throw $this->createNotFoundException('Unable to find Reservation entity.');
        }


        if(!$reservation->getInvoice()) {
            $localeEnglish = "en_US"; // Or en_GB

            $entity = new Invoice();
            $agency = $reservation->getOperator()->getAgency();
            $search = $reservation->getCheckOutProcess()->getSearch();
            $entity->setPayerAddress($agency->getAddress());
            $entity->setPayerCity($agency->getCity());
            $entity->setPayerState($agency->getState());
            $entity->setPayerCountry($agency->getCountry());
            $entity->setPayerZipCode($agency->getZipCode());
            $entity->setPayerName($agency->getName());
            $entity->setPayerTaxId($agency->getTaxPayerId());
            $entity->setReservation($reservation);
            $entity->setTotal($reservation->getTotal());
            $em->persist($entity);

            $conv = new Numbers_Words();
            $item = new InvoiceItem();
            $item->setDetail( '<strong>'. $reservation->getCheckOutProcess()->getGuestFullName()
                . ' X ' . ($search->getRoom1Adults() + $search->getRoom1Children())
                . ' - Confirmation ' . $reservation->getHotelFileId()
                . '</strong></br></br>'
                . '1 (one)' . $reservation->getRoom()->getName() . ' ' . $reservation->getHotel()->getName()
                . '</br>IN: ' . $search->getCheckInDate()->format('M.j') . ' - OUT: ' . $search->getCheckOutDate()->format('M.j')
                . ' - ' . $search->getNights() . ' (' . $conv->toWords($search->getNights(), $localeEnglish) . ') nights');
            $item->setAmount($reservation->getTotal());
            // if it was a many-to-one relationship, remove the relationship like this
            $item->setInvoice($entity);
            $entity->addItem($item);

            $em->persist($item);
            $reservation->setInvoice($entity);
            $em->persist($reservation);
            $em->flush();
        }
        else {
            $entity = $reservation->getInvoice();
        }
        $pdfBuilder = $this->get('knp_snappy.pdf');
        $html = $this->renderView('GushhBackendBundle:Invoice:download.html.twig',
            [
                'entity' => $entity,
            ]);

        $filename = 'Invoice-' . $entity->getId() . '.pdf';
        $response = new Response($pdfBuilder->getOutputFromHtml($html));
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename
        );

        return $response;
    }

        /**
     * Creates a form to delete a Reservation entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'reservationDeleteForm')))
            ->setAction($this->generateUrl('backend_reservation_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
            // ->add('submit', 'submit', array('label' => 'Delete'))
        ;
    }

    private function buildStayDate(ReservationDate $reservationDate) {
        return [
            'date'          => $reservationDate->getDate(),
            'day'           => $reservationDate->getDay(),
            'nightTax'      => $reservationDate->getTax(),
            'nightOccupancyTax' => $reservationDate->getNightOccupancyTax(),
            'priceNetRemaining' => $reservationDate->getPriceNet(),
            'priceNet'      => $reservationDate->getPriceNetRemaining(),
            'mandatoryServices' => $reservationDate->getMandatoryServices(),
            'mandatoryServicesRemaining' => $reservationDate->getMandatoryServicesRemaining(),
            'profit'        => $reservationDate->getProfit(),
            'cancellationPenalty' => $reservationDate->getCancellationPenalty(),
            'isBlackedOut'  => $reservationDate->getIsBlackedOut(),
            'isPremium'     => $reservationDate->getIsPremium(),
            'onRequest'     => $reservationDate->getOnRequest(),
            'isPromotion'   => $reservationDate->getIsPromotion(),

            'totalNet'      => $reservationDate->getTotalNet(),
            'totalWithServicesNet' => $reservationDate->getTotalWithServicesNet(),

            // Prices
            'price'         => $reservationDate->getPrice(),
            'tax'           => $reservationDate->getTax(),
            'commission'    => $reservationDate->getCommission(),
            'total'         => $reservationDate->getTotal(),
            'dateWithDiscount' => $reservationDate->getDateWithDiscount(),
            'isFree'        => $reservationDate->getIsFree(),
            'totalWithServices' => $reservationDate->getTotalWithServices(),
            'totalWithServicesAndCommission' => $reservationDate->getTotalWithServicesAndCommission()
        ];
    }
}


