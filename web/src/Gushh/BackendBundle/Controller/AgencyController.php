<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\Agency;
use Gushh\CoreBundle\Form\AgencyType;

/**
 * Agency controller.
 *
 * @Route("/dashboard/agenc")
 */
class AgencyController extends Controller
{

  /**
   * Lists all Agency entities.
   *
   * @Route("ies/", name="backend_agencies")
   * @Method("GET")
   * @Template()
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();
    $entities = $em->getRepository('GushhCoreBundle:Agency')->findAll();

    return [
      'entities' => $entities,
    ];

  }

  /**
   * Creates a new Agency entity.
   *
   * @Route("y/", name="backend_agency_create")
   * @Method("POST")
   * @Template("GushhBackendBundle:Agency:new.html.twig")
   */
  public function createAction(Request $request)
  {
    $entity = new Agency();
    $form = $this->createCreateForm($entity);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($entity);
      $em->flush();

      return $this->redirect($this->generateUrl('backend_agencies'));
    }

    return [
      'entity' => $entity,
      'form'   => $form->createView(),
    ];

  }

  /**
   * Creates a form to create a Agency entity.
   *
   * @param Agency $entity The entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createCreateForm(Agency $entity)
  {
    $form = $this->createForm(new AgencyType(), $entity, [
      'action' => $this->generateUrl('backend_agency_create'),
      'attr' => [
        'id'            => 'agencyForm',
        'autocomplete'  => 'off'
      ],
      'method' => 'POST',
    ]);

    return $form;
  }

  /**
   * Displays a form to create a new Agency entity.
   *
   * @Route("y/new/", name="backend_agency_new")
   * @Method("GET")
   * @Template()
   */
  public function newAction()
  {
    $entity = new Agency();
    $form   = $this->createCreateForm($entity);

    return [
      'entity' => $entity,
      'form'   => $form->createView(),
    ];
  }

  /**
   * Finds and displays a Agency entity.
   *
   * @Route("y/{id}/", name="backend_agency_show")
   * @Method("GET")
   * @Template()
   */
  public function showAction($id)
  {

    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('GushhCoreBundle:Agency')->find($id);

    if (!$entity)
      throw $this->createNotFoundException('Unable to find Agency entity.');

    $deleteForm = $this->createDeleteForm($id);

    return [
      'entity'      => $entity,
      'delete_form' => $deleteForm->createView(),
    ];

  }

  /**
   * Displays a form to edit an existing Agency entity.
   *
   * @Route("y/{id}/edit/", name="backend_agency_edit")
   * @Method("GET")
   * @Template()
   */
  public function editAction($id)
  {

    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('GushhCoreBundle:Agency')->find($id);

    if (!$entity)
      throw $this->createNotFoundException('Unable to find Agency entity.');

    $editForm = $this->createEditForm($entity);
    $deleteForm = $this->createDeleteForm($id);

    return [
      'entity'      => $entity,
      'entities'    => $entity->getUsers(),
      'form'        => $editForm->createView(),
      'deleteForm'  => $deleteForm->createView(),
    ];

  }

  /**
  * Creates a form to edit a Agency entity.
  *
  * @param Agency $entity The entity
  *
  * @return \Symfony\Component\Form\Form The form
  */
  private function createEditForm(Agency $entity)
  {

    $form = $this->createForm(new AgencyType(), $entity, [
      'action' => $this->generateUrl('backend_agency_update', ['id' => $entity->getId()]),
      'attr' => [
        'id'            => 'agencyForm',
        'autocomplete'  => 'off'
      ],
      'method' => 'PUT',
    ]);

    return $form;

  }

  /**
   * Edits an existing Agency entity.
   *
   * @Route("y/{id}/", name="backend_agency_update")
   * @Method("PUT")
   * @Template("GushhBackendBundle:Agency:edit.html.twig")
   */
  public function updateAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();
    $entity = $em->getRepository('GushhCoreBundle:Agency')->find($id);

    if (!$entity)
      throw $this->createNotFoundException('Unable to find Agency entity.');

    $deleteForm = $this->createDeleteForm($id);
    $editForm = $this->createEditForm($entity);
    $editForm->handleRequest($request);

    if ($editForm->isValid()) {
      $em->flush();
      return $this->redirect($this->generateUrl('backend_agencies'));
    }

    return [
      'entity'      => $entity,
      'edit_form'   => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ];

  }

  /**
   * Deletes a Agency entity.
   *
   * @Route("y/{id}/", name="backend_agency_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, $id)
  {
    $form = $this->createDeleteForm($id);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository('GushhCoreBundle:Agency')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Agency entity.');
      }

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('backend_agencies'));
  }

  /**
   * Creates a form to delete a Agency entity by id.
   *
   * @param mixed $id The entity id
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm($id)
  {
    return $this->createFormBuilder(null, ['attr' => ['id' => 'agencyDeleteForm']])
                ->setAction($this->generateUrl('backend_agency_delete', ['id' => $id]))
                ->setMethod('DELETE')
                ->getForm();

  }
}
