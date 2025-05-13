<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Gushh\CoreBundle\Entity\Promotion;
use Gushh\CoreBundle\Entity\PromotionExceptionPeriod;
use Gushh\CoreBundle\Form\PromotionType;
use Gushh\CoreBundle\Form\EditPromotionType;
/**
 * RoomPromotion controller.
 *
 * @Route("/dashboard/hotel")
 */
class RoomPromotionController extends Controller
{

    /**
     * Creates a new Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/", name="backend_room_promotion_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:RoomPromotion:new.html.twig")
     */
    public function createAction(Request $request, $hid, $rid)
    {
        $entity = new Promotion();
        $form = $this->createCreateForm($entity, $hid, $rid);
        $form->handleRequest($request);

        if ($form->isValid()) :
            $em = $this->getDoctrine()->getManager();
            $room = $em->getRepository('GushhCoreBundle:Room')->findOneById($rid);

            $entity->setRoom($room);

            $em->persist($entity);

            foreach ($entity->getExceptions() as $exception) {
                $exception->setPromotion($entity);
                $em->persist($exception);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $entity->getId()]));
        endif;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Promotion entity.
     *
     * @param Promotion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Promotion $entity, $hid, $rid)
    {
        return $this->createForm(new PromotionType(), $entity, array(
            'action' => $this->generateUrl('backend_room_promotion_create', ['hid' => $hid, 'rid' => $rid]),
            'attr' => array(
                'id' => 'roomPromotionForm'
            ),
            'method' => 'POST',
        ));
    }

    /**
     * Displays a form to create a new Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/new/", name="backend_room_promotion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($hid, $rid)
    {
        $entity = new Promotion();
        $form = $this->createCreateForm($entity, $hid, $rid);

        $em = $this->getDoctrine()->getManager();
        $room = $em->getRepository('GushhCoreBundle:Room')->find($rid);

        return array(
            'room' => $room,
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/", name="backend_room_promotion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($hid, $rid, $pid)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Promotion')->find($pid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Promotion entity.');
        }

        $exceptions = [];
        foreach ($entity->getExceptions() as $exception ) {
            $exceptions [] = (new \DateTime($exception->getPeriodFrom()))->format('d M Y') . ' to ' . (new \DateTime($exception->getPeriodTo()))->format('d M Y');
        }

        if($exceptions == [])
            $exceptions[] = 'No Exceptions';

        // $deleteForm = $this->createDeleteForm($hid, $rid, $pid);

        return array(
            'entity' => $entity,
            'exceptions' => $exceptions,
            //    'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/edit/", name="backend_room_promotion_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($hid, $rid, $pid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Promotion')->find($pid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Promotion entity.');
        }

        $editForm = $this->createEditForm($entity, $hid, $rid);
        $deleteForm = $this->createDeleteForm($hid, $rid, $pid);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Promotion entity.
     *
     * @param Promotion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Promotion $entity, $hid, $rid)
    {
        return $this->createForm(new EditPromotionType(), $entity, array(
            'action' => $this->generateUrl('backend_room_promotion_update', ['hid' => $hid, 'rid' => $rid, 'pid' => $entity->getId()]),
            'attr' => array(
                'id' => 'roomPromotionForm'
            ),
            'method' => 'PUT',
        ));
    }

    /**
     * Edits an existing Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/", name="backend_room_promotion_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:RoomPromotion:edit.html.twig")
     */
    public function updateAction(Request $request, $hid, $rid, $pid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Promotion')->find($pid);

        if (!$entity)
            throw $this->createNotFoundException('Unable to find Promotion entity.');

        $originalExceptions = new ArrayCollection();
        foreach ($entity->getExceptions() as $exception) {
            $originalExceptions->add($exception);
        }

        $editForm = $this->createEditForm($entity, $hid, $rid);
        $deleteForm = $this->createDeleteForm($hid, $rid, $pid);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {

            foreach ($originalExceptions as $exception) {
                if (false === $entity->getExceptions()->contains($exception)) {
                    // remove the Task from the Tag
                    $entity->getExceptions()->removeElement($exception);

                    // if it was a many-to-one relationship, remove the relationship like this
                    $exception->setPromotion(null);

                    $em->persist($exception);

                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($exception);
                }
            }

            foreach ($entity->getExceptions() as $exception) {
                if (false === $originalExceptions->contains($exception)) {
                    // remove the Task from the Tag

                    // if it was a many-to-one relationship, remove the relationship like this
                    $exception->setPromotion($entity);

                    $em->persist($exception);
                }
            }

            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid]));
        }

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/", name="backend_room_promotion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $hid, $rid, $pid)
    {
        $form = $this->createDeleteForm($hid, $rid, $pid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:Promotion')->find($pid);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Promotion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_room_show', ['hid' => $hid, 'rid' => $rid, 'tab' => 'promotions']));
    }

    /**
     * Creates a form to delete a Promotion entity by id.
     *
     * @param mixed $pid The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($hid, $rid, $pid)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'roomPromotionDeleteForm')))
            ->setAction($this->generateUrl('backend_room_promotion_delete', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
