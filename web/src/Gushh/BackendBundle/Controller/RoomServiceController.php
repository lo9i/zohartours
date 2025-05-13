<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\RoomService;
use Gushh\CoreBundle\Form\RoomServiceType;

/**
 * RoomService controller.
 *
 * @Route("/dashboard/hotel")
 */
class RoomServiceController extends Controller
{

    /**
     * Creates a new RoomService entity.
     *
     * @Route("/{hid}/room/{rid}/service/", name="backend_room_service_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:RoomService:new.html.twig")
     */
    public function createAction(Request $request, $hid, $rid)
    {
        $em = $this->getDoctrine()->getManager();
        $room = $em->getRepository('GushhCoreBundle:Room')->findOneById($rid);

        $entity = new RoomService();
        $entity->setRoom($room);
        
        $form = $this->createCreateForm($entity, $hid, $rid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_show', ['hid' => $hid, 'rid' => $rid, 'tab' => 'services']));
        }

        return array(
            'room' => $room,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a RoomService entity.
     *
     * @param RoomService $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RoomService $entity, $hid, $rid)
    {
        $form = $this->createForm(new RoomServiceType(), $entity, array(
            'action' => $this->generateUrl('backend_room_service_create', ['hid' => $hid, 'rid' => $rid]),
            'attr' => array(
                'id'    => 'roomServiceForm'
                ),
            'method' => 'POST',
        ));

        // $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new RoomService entity.
     *
     * @Route("/{hid}/room/{rid}/service/new/", name="backend_room_service_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($hid, $rid)
    {
        $entity = new RoomService();
        $form   = $this->createCreateForm($entity, $hid, $rid);

        $em = $this->getDoctrine()->getManager();
        $room = $em->getRepository('GushhCoreBundle:Room')->find($rid);

        return array(
            'room'  => $room,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a RoomService entity.
     *
     * @Route("/{hid}/room/{rid}/service/{sid}/", name="backend_room_service_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($hid, $rid, $sid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:RoomService')->find($sid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RoomService entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid, $sid);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing RoomService entity.
     *
     * @Route("/{hid}/room/{rid}/service/{sid}/edit/", name="backend_room_service_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($hid, $rid, $sid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:RoomService')->find($sid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RoomService entity.');
        }

        $editForm = $this->createEditForm($entity, $hid, $rid);
        $deleteForm = $this->createDeleteForm($hid, $rid, $sid);

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'deleteForm'  => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a RoomService entity.
    *
    * @param RoomService $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(RoomService $entity, $hid, $rid)
    {
        $form = $this->createForm(new RoomServiceType(), $entity, array(
            'action' => $this->generateUrl('backend_room_service_update', ['hid' => $hid, 'rid' => $rid, 'sid' => $entity->getId()]),
            'attr' => array(
                'id'    => 'roomServiceForm'
                ),
            'method' => 'PUT',
        ));

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    /**
     * Edits an existing RoomService entity.
     *
     * @Route("/{hid}/room/{rid}/service/{sid}/", name="backend_room_service_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:RoomService:edit.html.twig")
     */
    public function updateAction(Request $request, $hid, $rid, $sid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:RoomService')->find($sid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RoomService entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid, $sid);
        $editForm = $this->createEditForm($entity, $hid, $rid);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_show', ['hid' => $hid, 'rid' => $rid, 'tab' => 'services']));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a RoomService entity.
     *
     * @Route("/{hid}/room/{rid}/service/{sid}/", name="backend_room_service_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $hid, $rid, $sid)
    {
        $form = $this->createDeleteForm($hid, $rid, $sid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:RoomService')->find($sid);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find RoomService entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_room_show', ['hid' => $hid, 'rid' => $rid, 'tab' => 'services']));
    }

    /**
     * Creates a form to delete a RoomService entity by id.
     *
     * @param mixed $sid The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($hid, $rid, $sid)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'roomServiceDeleteForm')))
            ->setAction($this->generateUrl('backend_room_service_delete', ['hid' => $hid, 'rid' => $rid, 'sid' => $sid]))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
