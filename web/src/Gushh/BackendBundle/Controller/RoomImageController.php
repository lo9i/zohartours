<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\RoomImage;
use Gushh\CoreBundle\Form\RoomImageType;

/**
 * RoomImage controller.
 *
 * @Route("/dashboard/hotel")
 */
class RoomImageController extends Controller
{

    /**
     * Creates a new RoomImage entity.
     *
     * @Route("/{hid}/room/{rid}/image/", name="backend_room_image_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:RoomImage:new.html.twig")
     */
    public function createAction(Request $request, $hid, $rid)
    {
        $entity = new RoomImage();
        $form = $this->createCreateForm($entity, $hid, $rid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $room = $em->getRepository('GushhCoreBundle:Room')->findOneById($rid);
            $entity->setRoom($room);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_show', ['hid' => $hid, 'rid' => $rid, 'tab' => 'images']));
        }

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Creates a form to create a RoomImage entity.
     *
     * @param RoomImage $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RoomImage $entity, $hid, $rid)
    {
        $form = $this->createForm(new RoomImageType(), $entity, array(
            'action' => $this->generateUrl('backend_room_image_create', ['hid' => $hid, 'rid' => $rid]),
            'attr' => [
                'id'    => 'roomImageForm'
                ],
            'method' => 'POST',
        ));

        // $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new RoomImage entity.
     *
     * @Route("/{hid}/room/{rid}/image/new/", name="backend_room_image_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($hid, $rid)
    {
        $entity = new RoomImage();
        $form   = $this->createCreateForm($entity, $hid, $rid);

        $em = $this->getDoctrine()->getManager();
        $room = $em->getRepository('GushhCoreBundle:Room')->find($rid);

        return [
            'room' => $room,
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing RoomImage entity.
     *
     * @Route("/{hid}/room/{rid}/image/{iid}/edit/", name="backend_room_image_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($hid, $rid, $iid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:RoomImage')->find($iid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RoomImage entity.');
        }

        $editForm = $this->createEditForm($entity, $hid, $rid);
        $deleteForm = $this->createDeleteForm($hid, $rid, $iid);

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'deleteForm'  => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a RoomImage entity.
    *
    * @param RoomImage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(RoomImage $entity, $hid, $rid)
    {
        $form = $this->createForm(new RoomImageType(), $entity, [
            'action' => $this->generateUrl('backend_room_image_update', ['hid' => $hid, 'rid' => $rid, 'iid' => $entity->getId()]),
            'attr' => [
                'id'    => 'roomImageForm'
                ],
            'method' => 'PUT',
        ]);

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing RoomImage entity.
     *
     * @Route("/{hid}/room/{rid}/image/{iid}/", name="backend_room_image_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:RoomImage:edit.html.twig")
     */
    public function updateAction(Request $request, $hid, $rid, $iid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:RoomImage')->find($iid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RoomImage entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid, $iid);
        $editForm = $this->createEditForm($entity, $hid, $rid);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_show', ['hid' => $hid, 'rid' => $rid, 'tab' => 'images']));
        }

        return [
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }
    
    /**
     * Deletes a RoomImage entity.
     *
     * @Route("/{hid}/room/{rid}/image/{iid}/", name="backend_room_image_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $hid, $rid, $iid)
    {
        $form = $this->createDeleteForm($hid, $rid, $iid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:RoomImage')->find($iid);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find RoomImage entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_room_show', ['hid' => $hid, 'rid' => $rid, 'tab' => 'images']));
    }

    /**
     * Creates a form to delete a RoomImage entity by id.
     *
     * @param mixed $iid The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($hid, $rid, $iid)
    {
        return $this->createFormBuilder(null, ['attr' => ['id' => 'roomImageDeleteForm']])
            ->setAction($this->generateUrl('backend_room_image_delete', ['hid' => $hid, 'rid' => $rid, 'iid' => $iid]))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    
}
