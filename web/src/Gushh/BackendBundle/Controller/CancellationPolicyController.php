<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\CancellationPolicy;
use Gushh\CoreBundle\Form\CancellationPolicyType;

/**
 * CancellationPolicy controller.
 *
 * @Route("/dashboard/hotel")
 */
class CancellationPolicyController extends Controller
{

  /**
   * Creates a new CancellationPolicy entity.
   *
   * @Route("/{hid}/cancellation-policy/", name="backend_hotel_cancellation_policy_create")
   * @Method("POST")
   * @Template("GushhBackendBundle:CancellationPolicy:new.html.twig")
   */
  public function createAction(Request $request, $hid)
  {

    $entity = new CancellationPolicy();
    $form = $this->createCreateForm($entity, $hid);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $em     = $this->getDoctrine()->getManager();
      $hotel  = $em->getRepository('GushhCoreBundle:Hotel')->findOneById($hid);
      $entity->setHotel($hotel);
      $em->persist($entity);
      $em->flush();

      return $this->redirect($this->generateUrl('backend_hotel_show', ['hid' => $hid, 'tab' => 'policies']));
    }

    $em = $this->getDoctrine()->getManager();
    $hotel = $em->getRepository('GushhCoreBundle:Hotel')->find($hid);

    return [
      'hotel'  => $hotel,
      'entity' => $entity,
      'form'   => $form->createView(),
    ];

  }

  /**
   * Creates a form to create a CancellationPolicy entity.
   *
   * @param CancellationPolicy $entity The entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createCreateForm(CancellationPolicy $entity, $hid)
  {
    $form = $this->createForm(new CancellationPolicyType(), $entity, [
      'action' => $this->generateUrl('backend_hotel_cancellation_policy_create', ['hid' => $hid]),
      'attr' => [
        'id'  => 'cancellationPolicyForm'
      ],
      'method' => 'POST',
    ]);

    return $form;
  }

  /**
   * Displays a form to create a new CancellationPolicy entity.
   *
   * @Route("/{hid}/cancellation-policy/new/", name="backend_hotel_cancellation_policy_new")
   * @Method("GET")
   * @Template()
   */
  public function newAction($hid)
  {

    $entity = new CancellationPolicy();
    $form   = $this->createCreateForm($entity, $hid);

    $em     = $this->getDoctrine()->getManager();
    $hotel  = $em->getRepository('GushhCoreBundle:Hotel')->find($hid);

    return [
      'hotel'  => $hotel,
      'entity' => $entity,
      'form'   => $form->createView(),
    ];

  }

  /**
   * Finds and displays a CancellationPolicy entity.
   *
   * @Route("/{hid}/cancellation-policy/{cpid}/", name="backend_hotel_cancellation_policy_show")
   * @Method("GET")
   * @Template()
   */
  public function showAction($hid, $cpid)
  {

    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('GushhCoreBundle:CancellationPolicy')->find($cpid);

    if (!$entity) {
      throw $this->createNotFoundException('Unable to find CancellationPolicy entity.');
    }

    $deleteForm = $this->createDeleteForm($hid, $cpid);

    return [
      'entity'      => $entity,
      'delete_form' => $deleteForm->createView(),
    ];

  }

  /**
   * Displays a form to edit an existing CancellationPolicy entity.
   *
   * @Route("/{hid}/cancellation-policy/{cpid}/edit/", name="backend_hotel_cancellation_policy_edit")
   * @Method("GET")
   * @Template()
   */
  public function editAction($hid, $cpid)
  {

    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('GushhCoreBundle:CancellationPolicy')->find($cpid);

    if (!$entity) {
      throw $this->createNotFoundException('Unable to find CancellationPolicy entity.');
    }

    $editForm = $this->createEditForm($entity, $hid);
    $deleteForm = $this->createDeleteForm($hid, $cpid);

    return [
      'entity'      => $entity,
      'form'        => $editForm->createView(),
      'deleteForm'  => $deleteForm->createView(),
    ];

  }

  /**
  * Creates a form to edit a CancellationPolicy entity.
  *
  * @param CancellationPolicy $entity The entity
  *
  * @return \Symfony\Component\Form\Form The form
  */
  private function createEditForm(CancellationPolicy $entity, $hid)
  {

    $form = $this->createForm(new CancellationPolicyType(), $entity, [
      'action' => $this->generateUrl('backend_hotel_cancellation_policy_update', ['hid' => $hid, 'cpid' => $entity->getId()]),
      'attr' => [
        'id'    => 'cancellationPolicyForm'
      ],
      'method' => 'PUT',
    ]);

    return $form;

  }
  
  /**
   * Edits an existing CancellationPolicy entity.
   *
   * @Route("/{hid}/cancellation-policy/{cpid}/", name="backend_hotel_cancellation_policy_update")
   * @Method("PUT")
   * @Template("GushhBackendBundle:CancellationPolicy:edit.html.twig")
   */
  public function updateAction(Request $request, $hid, $cpid)
  {
      
    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('GushhCoreBundle:CancellationPolicy')->find($cpid);

    if (!$entity) {
      throw $this->createNotFoundException('Unable to find CancellationPolicy entity.');
    }

    $deleteForm = $this->createDeleteForm($hid, $cpid);
    $editForm = $this->createEditForm($entity, $hid);
    $editForm->handleRequest($request);

    if ($editForm->isValid()) {
      $em->flush();
      return $this->redirect($this->generateUrl('backend_hotel_show', ['hid' => $hid, 'tab' => 'policies']));
    }

    return [
      'entity'      => $entity,
      'edit_form'   => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ];

  }
  
  /**
   * Deletes a CancellationPolicy entity.
   *
   * @Route("/{hid}/cancellation-policy/{cpid}/", name="backend_hotel_cancellation_policy_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, $hid, $cpid)
  {

    $form = $this->createDeleteForm($hid, $cpid);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository('GushhCoreBundle:CancellationPolicy')->find($cpid);

      if (!$entity) {
          throw $this->createNotFoundException('Unable to find CancellationPolicy entity.');
      }

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('backend_hotel_show', ['hid' => $hid, 'tab' => 'policies']));

  }

  /**
   * Creates a form to delete a CancellationPolicy entity by id.
   *
   * @param mixed $cpid The entity id
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm($hid, $cpid)
  {
    return $this->createFormBuilder(null, ['attr' => ['id' => 'cancellationPolicyDeleteForm']])
                ->setAction($this->generateUrl('backend_hotel_cancellation_policy_delete', [
                  'hid' => $hid, 
                  'cpid' => $cpid
                ]))
                ->setMethod('DELETE')
                ->getForm();
                
  }
}
