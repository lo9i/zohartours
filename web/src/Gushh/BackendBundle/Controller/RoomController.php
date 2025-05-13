<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\Room;
use Gushh\CoreBundle\Form\RoomType;

/**
 * Room controller.
 *
 * @Route("/dashboard/hotel")
 */
class RoomController extends Controller
{

    /**
     * Lists all Room entities.
     *
     * @Route("/room/", name="backend_rooms")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GushhCoreBundle:Room')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Room entity.
     *
     * @Route("/{hid}/room/", name="backend_room_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:Room:new.html.twig")
     */
    public function createAction(Request $request, $hid)
    {
        $entity = new Room();
        $form = $this->createCreateForm($entity, $hid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $hotel = $em->getRepository('GushhCoreBundle:Hotel')->findOneById($hid);
            $entity->setHotel($hotel);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_hotel_show', array('hid' => $hid, 'tab' => 'rooms')));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Room entity.
     *
     * @param Room $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Room $entity, $hid)
    {
        $form = $this->createForm(new RoomType(), $entity, array(
            'action' => $this->generateUrl('backend_room_create', ['hid' => $hid]),
            'attr' => array(
                'id'    => 'roomForm'
                ),
            'method' => 'POST',
        ));

        // $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Room entity.
     *
     * @Route("/{hid}/room/new/", name="backend_room_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($hid)
    {
        $entity = new Room();
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
     * Finds and displays a Room entity.
     *
     * @Route("/{hid}/room/{rid}/", name="backend_room_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($hid, $rid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Room')->find($rid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Room entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid);

        return array(
            'entity'      => $entity,
            'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Room entity.
     *
     * @Route("/{hid}/room/{rid}/edit/", name="backend_room_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($hid, $rid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Room')->find($rid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Room entity.');
        }

        $editForm = $this->createEditForm($entity, $hid);
        $deleteForm = $this->createDeleteForm($hid, $rid);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Room entity.
    *
    * @param Room $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Room $entity, $hid)
    {
        $form = $this->createForm(new RoomType(), $entity, array(
            'action' => $this->generateUrl('backend_room_update', array('hid' => $hid, 'rid' => $entity->getId())),
            'attr' => array(
                'id'    => 'roomForm'
                ),
            'method' => 'PUT',
        ));

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Room entity.
     *
     * @Route("/{hid}/room/{rid}/", name="backend_room_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:Room:edit.html.twig")
     */
    public function updateAction(Request $request, $hid, $rid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Room')->find($rid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Room entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid);
        $editForm = $this->createEditForm($entity, $hid);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            
            return $this->redirect($this->generateUrl('backend_room_show', array('hid' => $hid, 'rid' => $entity->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Room entity.
     *
     * @Route("/{hid}/room/{rid}/", name="backend_room_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $hid, $rid)
    {
        $form = $this->createDeleteForm($hid, $rid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:Room')->find($rid);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Room entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_hotel_show', ['hid' => $hid, 'tab' => 'rooms']));
    }

    /**
     * Creates a form to delete a Room entity by id.
     *
     * @param mixed $rid The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($hid, $rid)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'roomDeleteForm')))
            ->setAction($this->generateUrl('backend_room_delete', array('hid' => $hid, 'rid' => $rid)))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
