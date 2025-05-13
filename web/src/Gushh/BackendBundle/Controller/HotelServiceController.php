<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\HotelService;
use Gushh\CoreBundle\Form\HotelServiceType;

/**
 * HotelService controller.
 *
 * @Route("/dashboard/hotel")
 */
class HotelServiceController extends Controller
{

    /**
     * Lists all HotelService entities.
     *
     * @Route("/service/", name="backend_hotel_services")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GushhCoreBundle:HotelService')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new HotelService entity.
     *
     * @Route("/{hid}/service/", name="backend_hotel_service_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:HotelService:new.html.twig")
     */
    public function createAction(Request $request, $hid)
    {
        $entity = new HotelService();
        $form = $this->createCreateForm($entity, $hid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $hotel = $em->getRepository('GushhCoreBundle:Hotel')->findOneById($hid);
            $entity->setHotel($hotel);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_hotel_show', array('hid' => $hid, 'tab' => 'services')));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a HotelService entity.
     *
     * @param HotelService $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(HotelService $entity, $hid)
    {
        $form = $this->createForm(new HotelServiceType(), $entity, array(
            'action' => $this->generateUrl('backend_hotel_service_create', ['hid' => $hid]),
            'attr' => array(
                'id'    => 'hotelServiceForm'
                ),
            'method' => 'POST',
        ));

        // $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new HotelService entity.
     *
     * @Route("/{hid}/service/new/", name="backend_hotel_service_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($hid)
    {
        $entity = new HotelService();
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
     * Finds and displays a HotelService entity.
     *
     * @Route("/{hid}/service/{sid}/", name="backend_hotel_service_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($hid, $sid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:HotelService')->find($sid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HotelService entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $sid);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing HotelService entity.
     *
     * @Route("/{hid}/service/{sid}/edit/", name="backend_hotel_service_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($hid, $sid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:HotelService')->find($sid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HotelService entity.');
        }

        $editForm = $this->createEditForm($entity, $hid);
        $deleteForm = $this->createDeleteForm($hid, $sid);

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'deleteForm'  => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a HotelService entity.
    *
    * @param HotelService $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(HotelService $entity, $hid)
    {
        $form = $this->createForm(new HotelServiceType(), $entity, array(
            'action' => $this->generateUrl('backend_hotel_service_update', array('hid' => $hid, 'sid' => $entity->getId())),
            'attr' => array(
                'id'    => 'hotelServiceForm'
                ),
            'method' => 'PUT',
        ));

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing HotelService entity.
     *
     * @Route("/{hid}/service/{sid}/", name="backend_hotel_service_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:HotelService:edit.html.twig")
     */
    public function updateAction(Request $request, $hid, $sid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:HotelService')->find($sid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HotelService entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $sid);
        $editForm = $this->createEditForm($entity, $hid);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_hotel_show', array('hid' => $hid, 'tab' => 'services')));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a HotelService entity.
     *
     * @Route("/{hid}/service/{sid}/", name="backend_hotel_service_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $hid, $sid)
    {
        $form = $this->createDeleteForm($hid, $sid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:HotelService')->find($sid);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find HotelService entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_hotel_show', array('hid' => $hid, 'tab' => 'services')));
    }

    /**
     * Creates a form to delete a HotelService entity by id.
     *
     * @param mixed $sid The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($hid, $sid)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'hotelServiceDeleteForm')))
            ->setAction($this->generateUrl('backend_hotel_service_delete', array('hid' => $hid, 'sid' => $sid)))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
