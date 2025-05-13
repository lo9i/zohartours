<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\Date;
use Gushh\CoreBundle\Form\DateType;
use Gushh\CoreBundle\Form\EditDateType;
use Gushh\CoreBundle\Classes\Util;

/**
 * Date controller.
 *
 * @Route("/backend/hotel")
 */
class DateController extends Controller
{

    /**
     * Creates a new Date entity.
     *
     * @Route("/{hid}/room/{rid}/date/", name="backend_room_date_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:Date:new.html.twig")
     */
    public function createAction(Request $request, $hid, $rid)
    {

        $em = $this->getDoctrine()->getManager();
        $room = $em->getRepository('GushhCoreBundle:Room')->find($rid);

        $entity = new Date();
        $entity->setRoom($room);

        $form = $this->createCreateForm($entity, $hid, $rid);
        $form->handleRequest($request);

        if ($form->isValid()) {


            $range = Util::createRange($entity->getDateFrom(), $entity->getDateTo());

            // TODO REFACTOR
            // $name               = $entity->getName();
            $cutOff = $entity->getCutOff();
            $stock = $entity->getStock();
            $minimumStay = $entity->getMinimumStay();
            $maximumStay = $entity->getMaximumStay();
            $premiumDate = $entity->getPremiumDate();
            $dateFrom = $entity->getDateFrom();
            $dateTo = $entity->getDateTo();

            if ($entity->getDailyRates() == false) {
                $rate = $entity->getRate();
                $mondayRate = $rate;
                $tuesdayRate = $rate;
                $wednesdayRate = $rate;
                $thursdayRate = $rate;
                $fridayRate = $rate;
                $saturdayRate = $rate;
                $sundayRate = $rate;
            } else {
                $mondayRate = $entity->getMondayRate();
                $tuesdayRate = $entity->getTuesdayRate();
                $wednesdayRate = $entity->getWednesdayRate();
                $thursdayRate = $entity->getThursdayRate();
                $fridayRate = $entity->getFridayRate();
                $saturdayRate = $entity->getSaturdayRate();
                $sundayRate = $entity->getSundayRate();
            }

            $policy = $entity->getCancellationPolicy();

            if ($mondayRate == $tuesdayRate && $mondayRate == $wednesdayRate && $mondayRate == $thursdayRate && $mondayRate == $fridayRate && $mondayRate == $saturdayRate && $mondayRate == $sundayRate) {
                $dailyRate = false;
            } else {
                $dailyRate = true;
            }

            foreach ($range as $date) {

                $existDate = $em->getRepository('GushhCoreBundle:Date')->findDateByRoom($date, $rid);

                if (!$existDate) {
                    $newDate = new Date();
                    // $newDate->setName($name);
                    $newDate->setDate($date);
                    $newDate->setCutOff($cutOff);
                    $newDate->setStock($stock);
                    $newDate->setMinimumStay($minimumStay);
                    $newDate->setMaximumStay($maximumStay);
                    $newDate->setPremiumDate($premiumDate);
                    $newDate->setMondayRate($mondayRate);
                    $newDate->setTuesdayRate($tuesdayRate);
                    $newDate->setWednesdayRate($wednesdayRate);
                    $newDate->setThursdayRate($thursdayRate);
                    $newDate->setFridayRate($fridayRate);
                    $newDate->setSaturdayRate($saturdayRate);
                    $newDate->setSundayRate($sundayRate);
                    $newDate->setDailyRates($dailyRate);
                    $newDate->setCancellationPolicy($policy);
                    $newDate->setRoom($room);

                    $newDate->setDateFrom($dateFrom);
                    $newDate->setDateTo($dateTo);

                    $em->persist($newDate);
                } else {
                    // $existDate->setName($name);
                    $existDate->setCutOff($cutOff);
                    $existDate->setStock($stock);
                    $existDate->setMinimumStay($minimumStay);
                    $existDate->setMaximumStay($maximumStay);
                    $existDate->setPremiumDate($premiumDate);
                    $existDate->setMondayRate($mondayRate);
                    $existDate->setTuesdayRate($tuesdayRate);
                    $existDate->setWednesdayRate($wednesdayRate);
                    $existDate->setThursdayRate($thursdayRate);
                    $existDate->setFridayRate($fridayRate);
                    $existDate->setSaturdayRate($saturdayRate);
                    $existDate->setSundayRate($sundayRate);
                    $existDate->setDailyRates($dailyRate);
                    $existDate->setCancellationPolicy($policy);

                    $existDate->setDateFrom($dateFrom);
                    $existDate->setDateTo($dateTo);


                    $em->persist($existDate);
                }

            }

            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_show', [
                    'hid' => $hid,
                    'rid' => $rid,
                    'tab' => 'dates'
                ]
            ));

        }

        $errors = $form->getErrorsAsString();

        return [
            'errors' => $errors,
            'room' => $room,
            'entity' => $entity,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a form to create a Date entity.
     *
     * @param Date $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Date $entity, $hid, $rid)
    {
        $form = $this->createForm(new DateType(), $entity, array(
            'action' => $this->generateUrl('backend_room_date_create', ['hid' => $hid, 'rid' => $rid]),
            'attr' => array(
                'id' => 'dateForm'
            ),
            'method' => 'POST',
        ));

        // $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Date entity.
     *
     * @Route("/{hid}/room/{rid}/date/new/", name="backend_room_date_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($hid, $rid)
    {
        $em = $this->getDoctrine()->getManager();
        $room = $em->getRepository('GushhCoreBundle:Room')->find($rid);
        $hotel = $room->getHotel();

        $policies = count($hotel->getPolicies());
        $rates = count($room->getRates());
        if ($rates == 0 or $policies == 0) {
            return $this->redirect($this->generateUrl('backend_room_show', ['hid' => $hid, 'rid' => $rid, 'tab' => 'dates']));
        }

        $entity = new Date();
        $entity->setRoom($room);

        $form = $this->createCreateForm($entity, $hid, $rid);

        return array(
            'room' => $room,
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Date entity.
     *
     * @Route("/{hid}/room/{rid}/date/{did}/", name="backend_room_date_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($hid, $rid, $did)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Date')->find($did);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Date entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid, $did);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Date entity.
     *
     * @Route("/{hid}/room/{rid}/date/{did}/edit/", name="backend_room_date_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($hid, $rid, $did)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Date')->find($did);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Date entity.');
        }

        $editForm = $this->createEditForm($entity, $hid, $rid);
        $deleteForm = $this->createDeleteForm($hid, $rid, $did);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Date entity.
     *
     * @param Date $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Date $entity, $hid, $rid)
    {
        $form = $this->createForm(new EditDateType(), $entity, array(
            'action' => $this->generateUrl('backend_room_date_update', ['hid' => $hid, 'rid' => $rid, 'did' => $entity->getId()]),
            'attr' => array(
                'id' => 'dateForm'
            ),
            'method' => 'PUT',
        ));

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Date entity.
     *
     * @Route("/{hid}/room/{rid}/date/{did}/", name="backend_room_date_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:Date:edit.html.twig")
     */
    public function updateAction(Request $request, $hid, $rid, $did)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Date')->find($did);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Date entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid, $did);
        $editForm = $this->createEditForm($entity, $hid, $rid);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $rate = $editForm->get('rate')->getData();
            $policy = $editForm->get('cancellationPolicy')->getData();


            // $entity->setCutOff($cutOff);
            // $entity->setStock($stock);
            // $entity->setMinimumStay($minimumStay);
            // $entity->setMaximumStay($maximumStay);
            // $entity->setPremiumDate($premiumDate);
            $entity->setMondayRate($rate);
            $entity->setTuesdayRate($rate);
            $entity->setWednesdayRate($rate);
            $entity->setThursdayRate($rate);
            $entity->setFridayRate($rate);
            $entity->setSaturdayRate($rate);
            $entity->setSundayRate($rate);
            $entity->setDailyRates(true);
            $entity->setCancellationPolicy($policy);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_show', ['hid' => $hid, 'rid' => $rid, 'tab' => 'dates']));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Date entity.
     *
     * @Route("/{hid}/room/{rid}/date/{did}/", name="backend_room_date_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $hid, $rid, $did)
    {
        $form = $this->createDeleteForm($hid, $rid, $did);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:Date')->find($did);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Date entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_room_show', ['hid' => $hid, 'rid' => $rid, 'tab' => 'dates']));
    }

    /**
     * Creates a form to delete a Date entity by id.
     *
     * @param mixed $did The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($hid, $rid, $did)
    {
        return $this->createFormBuilder(null, array('attr' => ['id' => 'dateDeleteForm']))
            ->setAction($this->generateUrl('backend_room_date_delete', ['hid' => $hid, 'rid' => $rid, 'did' => $did]))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    /**
     * @Route("/hotel/{hid}/date/{did}/", name="backend_room_date_stop", defaults={"did" = 0})
     * @Method("POST")
     */
    public function stopSellAction($hid, $did)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Date')->find($did);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Date entity.');
        }
        
        $entity->setStopSell(!$entity->getStopSell());

        $em->persist($entity);
        $em->flush();

        return new Response('Success');
    }
}

