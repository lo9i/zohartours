<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\Rate;
use Gushh\CoreBundle\Form\RateType;

/**
 * Rate controller.
 *
 * @Route("/dashboard/hotel")
 */
class RateController extends Controller
{

    /**
     * Creates a new Rate entity.
     *
     * @Route("/{hid}/room/{rid}/rate/", name="backend_room_rate_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:Rate:new.html.twig")
     */
    public function createAction(Request $request, $hid, $rid)
    {
        $entity = new Rate();
        $form = $this->createCreateForm($entity, $hid, $rid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $room = $em->getRepository('GushhCoreBundle:Room')->findOneById($rid);
            $entity->setRoom($room);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_show', ['hid' => $hid, 'rid' => $rid, 'tab' => 'rates']));
        }

        $em = $this->getDoctrine()->getManager();
        $room = $em->getRepository('GushhCoreBundle:Room')->find($rid);

        $errors = $form->getErrors();


        return array(
            'errors' => $errors,
            'room' => $room,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Rate entity.
     *
     * @param Rate $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Rate $entity, $hid, $rid)
    {
        $form = $this->createForm(new RateType(), $entity, array(
            'action' => $this->generateUrl('backend_room_rate_create', ['hid' => $hid, 'rid' => $rid]),
            'attr' => array(
                'id'    => 'rateForm'
                ),
            'method' => 'POST',
        ));

        // $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Rate entity.
     *
     * @Route("/{hid}/room/{rid}/rate/new/", name="backend_room_rate_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($hid, $rid)
    {
        $entity = new Rate();
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
     * Finds and displays a Rate entity.
     *
     * @Route("/{hid}/room/{rid}/rate/{raid}/", name="backend_room_rate_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($hid, $rid, $raid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Rate')->find($raid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rate entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid, $raid);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Rate entity.
     *
     * @Route("/{hid}/room/{rid}/rate/{raid}/edit/", name="backend_room_rate_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($hid, $rid, $raid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Rate')->find($raid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rate entity.');
        }

        $room = $em->getRepository('GushhCoreBundle:Room')->find($rid);
        $editForm = $this->createEditForm($entity, $hid, $rid);
        $deleteForm = $this->createDeleteForm($hid, $rid, $raid);

        return array(
            'entity'      => $entity,
            'room'        => $room,
            'form'        => $editForm->createView(),
            'deleteForm'  => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Rate entity.
    *
    * @param Rate $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Rate $entity, $hid, $rid)
    {
        $form = $this->createForm(new RateType(), $entity, array(
            'action' => $this->generateUrl('backend_room_rate_update', ['hid' => $hid, 'rid' => $rid, 'raid' => $entity->getId()]),
            'attr' => array(
                'id'    => 'rateForm'
                ),
            'method' => 'PUT',
        ));

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Rate entity.
     *
     * @Route("/{hid}/room/{rid}/rate/{raid}/", name="backend_room_rate_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:Rate:edit.html.twig")
     */
    public function updateAction(Request $request, $hid, $rid, $raid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Rate')->find($raid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rate entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid, $raid);
        $editForm = $this->createEditForm($entity, $hid, $rid);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_show', ['hid' => $hid, 'rid' => $rid, 'tab' => 'rates']));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a Rate entity.
     *
     * @Route("/{hid}/room/{rid}/rate/{raid}/", name="backend_room_rate_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $hid, $rid, $raid)
    {
        $form = $this->createDeleteForm($hid, $rid, $raid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:Rate')->find($raid);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Rate entity.');
            }

//            $em->remove($entity);
            $entity->setEnabled(false);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_room_show', ['hid' => $hid, 'rid' => $rid, 'tab' => 'rates']));
    }

    /**
     * Creates a form to delete a Rate entity by id.
     *
     * @param mixed $raid The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($hid, $rid, $raid)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'rateDeleteForm')))
            ->setAction($this->generateUrl('backend_room_rate_delete', ['hid' => $hid, 'rid' => $rid, 'raid' => $raid]))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
