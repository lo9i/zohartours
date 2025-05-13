<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\HotelPromotion;
use Gushh\CoreBundle\Form\HotelPromotionType;

/**
 * HotelPromotion controller.
 *
 * @Route("/dashboard/hotel")
 */
class HotelPromotionController extends Controller
{

    /**
     * Lists all HotelPromotion entities.
     *
     * @Route("/promotion/", name="backend_hotel_promotions")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GushhCoreBundle:HotelPromotion')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new HotelPromotion entity.
     *
     * @Route("/{hid}/promotion/", name="backend_hotel_promotion_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:HotelPromotion:new.html.twig")
     */
    public function createAction(Request $request, $hid)
    {
        $entity = new HotelPromotion();
        $form = $this->createCreateForm($entity, $hid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $hotel = $em->getRepository('GushhCoreBundle:Hotel')->findOneById($hid);
            $entity->setHotel($hotel);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_hotel_show', array('hid' => $hid, 'tab' => 'promotions')));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a HotelPromotion entity.
     *
     * @param HotelPromotion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(HotelPromotion $entity, $hid)
    {
        $form = $this->createForm(new HotelPromotionType(), $entity, array(
            'action' => $this->generateUrl('backend_hotel_promotion_create', ['hid' => $hid]),
            'attr' => array(
                'id'    => 'hotelPromotionForm'
                ),
            'method' => 'POST',
        ));

        // $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new HotelPromotion entity.
     *
     * @Route("/{hid}/promotion/new/", name="backend_hotel_promotion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($hid)
    {
        $entity = new HotelPromotion();
        $form   = $this->createCreateForm($entity, $hid);

        $em = $this->getDoctrine()->getManager();
        $hotel = $em->getRepository('GushhCoreBundle:Hotel')->find($hid);

        return array(
            'hotel'  => $hotel,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a HotelPromotion entity.
     *
     * @Route("/{hid}/promotion/{pid}/", name="backend_hotel_promotion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($hid, $pid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:HotelPromotion')->find($pid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HotelPromotion entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $pid);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing HotelPromotion entity.
     *
     * @Route("/{hid}/promotion/{pid}/edit/", name="backend_hotel_promotion_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($hid, $pid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:HotelPromotion')->find($pid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HotelPromotion entity.');
        }

        $editForm = $this->createEditForm($entity, $hid);
        $deleteForm = $this->createDeleteForm($hid, $pid);

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'deleteForm'  => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a HotelPromotion entity.
    *
    * @param HotelPromotion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(HotelPromotion $entity, $hid)
    {
        $form = $this->createForm(new HotelPromotionType(), $entity, array(
            'action' => $this->generateUrl('backend_hotel_promotion_update', array('hid' => $hid, 'pid' => $entity->getId())),
            'attr' => array(
                'id'    => 'hotelPromotionForm'
                ),
            'method' => 'PUT',
        ));

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing HotelPromotion entity.
     *
     * @Route("/{hid}/promotion/{pid}/", name="backend_hotel_promotion_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:HotelPromotion:edit.html.twig")
     */
    public function updateAction(Request $request, $hid, $pid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:HotelPromotion')->find($pid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HotelPromotion entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $pid);
        $editForm = $this->createEditForm($entity, $hid);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('backend_hotel_show', array('hid' => $hid, 'tab' => 'promotions')));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a HotelPromotion entity.
     *
     * @Route("/{hid}/promotion/{pid}/", name="backend_hotel_promotion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $hid, $pid)
    {
        $form = $this->createDeleteForm($hid, $pid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:HotelPromotion')->find($pid);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find HotelPromotion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_hotel_show', array('hid' => $hid, 'tab' => 'promotions')));
    }

    /**
     * Creates a form to delete a HotelPromotion entity by id.
     *
     * @param mixed $pid The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($hid, $pid)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'hotelPromotionDeleteForm')))
            ->setAction($this->generateUrl('backend_hotel_promotion_delete', array('hid' => $hid, 'pid' => $pid)))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
