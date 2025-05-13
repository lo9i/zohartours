<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\CityImage;
use Gushh\CoreBundle\Form\CityImageType;

/**
 * ConfigurationCity controller.
 *
 * @Route("/dashboard/frontend/city-image")
 */
class ConfigurationCityController extends Controller
{

  /**
   * Lists all City entities.
   *
   * @Route("s/", name="backend_configuration_city_images")
   * @Method("GET")
   * @Template()
   */
  public function indexAction()
  {

    $em = $this->getDoctrine()->getManager();

    $entities = $em->getRepository('GushhCoreBundle:CityImage')->findAll();

    return [
      'entities' => $entities,
    ];

  }
  
  /**
   * Displays a form to edit an existing CityImage entity.
   *
   * @Route("/{id}/edit/", name="backend_configuration_city_image_edit")
   * @Method("GET")
   * @Template()
   */
  public function editAction($id)
  {

    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('GushhCoreBundle:CityImage')->find($id);

    if (!$entity) {
      throw $this->createNotFoundException('Unable to find City entity.');
    }

    $editForm = $this->createEditForm($entity);
    $deleteForm = $this->createDeleteForm($id);

    return [
      'entity'      => $entity,
      'form'   => $editForm->createView(),
      'deleteForm' => $deleteForm->createView(),
    ];

  }

  /**
  * Creates a form to edit a City entity.
  *
  * @param CityImage $entity The entity
  *
  * @return \Symfony\Component\Form\Form The form
  */
  private function createEditForm(CityImage $entity)
  {

    $form = $this->createForm(new CityImageType(), $entity, [
        'action' => $this->generateUrl('backend_configuration_city_image_update', ['id' => $entity->getId()]),
        'attr' => [
          'id'            => 'cityImageForm',
          'autocomplete'  => 'off'
        ],
        'method' => 'PUT',
    ]);

    return $form;

  }

  /**
   * Edits an existing CityImage entity.
   *
   * @Route("/{id}/", name="backend_configuration_city_image_update")
   * @Method("PUT")
   * @Template("GushhBackendBundle:ConfigurationCity:edit.html.twig")
   */
  public function updateAction(Request $request, $id)
  {

    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('GushhCoreBundle:CityImage')->find($id);

    if (!$entity) {
        throw $this->createNotFoundException('Unable to find City entity.');
    }

    $deleteForm = $this->createDeleteForm($id);
    $editForm = $this->createEditForm($entity);
    $editForm->handleRequest($request);

    if ($editForm->isValid()) {
        $em->flush();
        return $this->redirect($this->generateUrl('backend_configuration_city_images'));
    }

    return [
      'entity'      => $entity,
      'editForm'    => $editForm->createView(),
      'deleteForm'  => $deleteForm->createView(),
    ];

  }

  /**
   * Deletes a CityImage entity.
   *
   * @Route("/{id}/", name="backend_configuration_city_image_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, $id)
  {

    $form = $this->createDeleteForm($id);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository('GushhCoreBundle:CityImage')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find City entity.');
      }

      $entity->setPath('default.png');
      $em->persist($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('backend_configuration_city_images'));

  }

  /**
   * Creates a form to delete a City entity by id.
   *
   * @param mixed $id The entity id
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm($id)
  {
      
    return $this->createFormBuilder(null, ['attr' => ['id' => 'cityImageDeleteForm']])
                ->setAction($this->generateUrl('backend_configuration_city_image_delete', ['id' => $id]))
                ->setMethod('DELETE')
                ->getForm();

  }
}
