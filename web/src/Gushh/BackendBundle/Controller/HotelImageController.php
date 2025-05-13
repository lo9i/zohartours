<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\HotelImage;
use Gushh\CoreBundle\Form\HotelImageType;

/**
 * HotelImage controller.
 *
 * @Route("/dashboard/hotel")
 */
class HotelImageController extends Controller
{

    /**
     * Creates a new HotelImage entity.
     *
     * @Route("/{hid}/image/", name="backend_hotel_image_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:HotelImage:new.html.twig")
     */
    public function createAction(Request $request, $hid)
    {
        $entity = new HotelImage();
        $form = $this->createCreateForm($entity, $hid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $hotel = $em->getRepository('GushhCoreBundle:Hotel')->findOneById($hid);
            $entity->setHotel($hotel);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_hotel_show', array('hid' => $hid, 'tab' => 'images')));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a HotelImage entity.
     *
     * @param HotelImage $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(HotelImage $entity, $hid)
    {
        $form = $this->createForm(new HotelImageType(), $entity, array(
            'action' => $this->generateUrl('backend_hotel_image_create', ['hid' => $hid]),
            'attr' => array(
                'id'    => 'hotelImageForm'
                ),
            'method' => 'POST',
        ));

        // $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new HotelImage entity.
     *
     * @Route("/{hid}/image/new/", name="backend_hotel_image_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($hid)
    {
        $entity = new HotelImage();
        $form   = $this->createCreateForm($entity, $hid);

        $em = $this->getDoctrine()->getManager();
        $hotel = $em->getRepository('GushhCoreBundle:Hotel')->find($hid);

        return array(
            'hotel' => $hotel,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing HotelImage entity.
     *
     * @Route("/{hid}/image/{iid}/edit/", name="backend_hotel_image_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($hid, $iid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:HotelImage')->find($iid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HotelImage entity.');
        }

        $editForm = $this->createEditForm($entity, $hid);
        $deleteForm = $this->createDeleteForm($hid, $iid);

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'deleteForm'  => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a HotelImage entity.
    *
    * @param HotelImage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(HotelImage $entity, $hid)
    {
        $form = $this->createForm(new HotelImageType(), $entity, array(
            'action' => $this->generateUrl('backend_hotel_image_update', array('hid' => $hid, 'iid' => $entity->getId())),
            'attr' => array(
                'id'    => 'hotelImageForm'
                ),
            'method' => 'PUT',
        ));

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing HotelImage entity.
     *
     * @Route("/{hid}/image/{iid}/", name="backend_hotel_image_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:HotelImage:edit.html.twig")
     */
    public function updateAction(Request $request, $hid, $iid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:HotelImage')->find($iid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HotelImage entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $iid);
        $editForm = $this->createEditForm($entity, $hid);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('backend_hotel_show', array('hid' => $hid, 'tab' => 'images')));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a HotelImage entity.
     *
     * @Route("/{hid}/image/{iid}/", name="backend_hotel_image_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $hid, $iid)
    {
        $form = $this->createDeleteForm($hid, $iid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:HotelImage')->find($iid);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find HotelImage entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_hotel_show', array('hid' => $hid, 'tab' => 'images')));
    }

    /**
     * Creates a form to delete a HotelImage entity by id.
     *
     * @param mixed $iid The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($hid, $iid)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'hotelImageDeleteForm')))
            ->setAction($this->generateUrl('backend_hotel_image_delete', array('hid' => $hid, 'iid' => $iid)))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    
}
