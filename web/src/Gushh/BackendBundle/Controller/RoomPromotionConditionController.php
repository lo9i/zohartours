<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Gushh\CoreBundle\Entity\Promotion;
use Gushh\CoreBundle\Entity\PromotionCondition;
use Gushh\CoreBundle\Form\PromotionConditionType;
use Gushh\CoreBundle\Form\PromotionConditionCumulativeType;

/**
 * RoomPromotionCondition controller.
 *
 * @Route("/dashboard/hotel")
 */
class RoomPromotionConditionController extends Controller
{

    /**
     * Creates a new PromotionCondition entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/condition/{type}/", name="backend_room_promotion_condition_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:RoomPromotionCondition:new.html.twig")
     */
    public function createAction(Request $request, $hid, $rid, $pid, $type)
    {
        $entity = new PromotionCondition();
        $form = $this->createCreateForm($entity, $hid, $rid, $pid, $type);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $promotion = $em->getRepository('GushhCoreBundle:Promotion')->findOneById($pid);
            $entity->setPromotion($promotion);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'tab' => 'conditions']));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Promotion entity.
     *
     * @param Promotion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PromotionCondition $entity, $hid, $rid, $pid, $type)
    {
        if ($type == 'cumulative') {
            $form = $this->createForm(new PromotionConditionCumulativeType(), $entity, array(
                'action' => $this->generateUrl('backend_room_promotion_condition_create', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'type' => $type]),
                'attr' => array(
                    'id'    => 'roomPromotionForm'
                    ),
                'method' => 'POST',
            ));
        } else {
            $form = $this->createForm(new PromotionConditionType(), $entity, array(
                'action' => $this->generateUrl('backend_room_promotion_condition_create', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'type' => $type]),
                'attr' => array(
                    'id'    => 'roomPromotionForm'
                    ),
                'method' => 'POST',
            ));
        }

        // $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/condition/{type}/new/", name="backend_room_promotion_condition_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($hid, $rid, $pid, $type)
    {
        $entity = new PromotionCondition();
        $form   = $this->createCreateForm($entity, $hid, $rid, $pid, $type);

        $em = $this->getDoctrine()->getManager();
        $promotion = $em->getRepository('GushhCoreBundle:Promotion')->find($pid);

        return array(
            'promotion'  => $promotion,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing PromotionCondition entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/condition/{type}/{cid}/edit/", name="backend_room_promotion_condition_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($hid, $rid, $pid, $cid, $type)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:PromotionCondition')->find($cid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PromotionCondition entity.');
        }

        $editForm = $this->createEditForm($entity, $hid, $rid, $pid, $cid, $type);
        $deleteForm = $this->createDeleteForm($hid, $rid, $pid, $cid);

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'deleteForm'  => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Promotion entity.
    *
    * @param Promotion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PromotionCondition $entity, $hid, $rid, $pid, $cid, $type)
    {

        if ($type == 'cumulative') {
            $form = $this->createForm(new PromotionConditionCumulativeType(), $entity, array(
                'action' => $this->generateUrl('backend_room_promotion_condition_update', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'cid' => $cid, 'type' => $type]),
                'attr' => array(
                    'id'    => 'roomPromotionForm'
                    ),
                'method' => 'PUT',
            ));
        } else {
            $form = $this->createForm(new PromotionConditionType(), $entity, array(
                'action' => $this->generateUrl('backend_room_promotion_condition_update', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'cid' => $cid, 'type' => $type]),
                'attr' => array(
                    'id'    => 'roomPromotionForm'
                    ),
                'method' => 'PUT',
            ));
        }

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/condition/{type}/{cid}/", name="backend_room_promotion_condition_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:RoomPromotionCondition:edit.html.twig")
     */
    public function updateAction(Request $request, $hid, $rid, $pid, $cid, $type)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:PromotionCondition')->find($cid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PromotionCondition entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid, $pid, $cid);
        $editForm = $this->createEditForm($entity, $hid, $rid, $pid, $cid, $type);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'tab' => 'conditions']));
        }

        return array(
            'entity'        => $entity,
            'form'          => $editForm->createView(),
            'deleteForm'    => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/condition/{cid}/", name="backend_room_promotion_condition_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $hid, $rid, $pid, $cid)
    {
        $form = $this->createDeleteForm($hid, $rid, $pid, $cid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:PromotionCondition')->find($cid);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Promotion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'tab' => 'conditions']));
    }

    /**
     * Creates a form to delete a Promotion entity by id.
     *
     * @param mixed $pid The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($hid, $rid, $pid, $cid)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'roomPromotionDeleteForm')))
            ->setAction($this->generateUrl('backend_room_promotion_condition_delete', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'cid' => $cid]))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
