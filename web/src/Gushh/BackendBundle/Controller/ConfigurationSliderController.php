<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\SliderImage;
use Gushh\CoreBundle\Form\SliderImageType;

/**
 * ConfigurationSlider controller.
 *
 * @Route("/dashboard/frontend/slider-image")
 */
class ConfigurationSliderController extends Controller
{

  /**
   * Lists all Slider entities.
   *
   * @Route("s/", name="backend_configuration_slider_images")
   * @Method("GET")
   * @Template()
   */
  public function indexAction()
  {

    $em = $this->getDoctrine()->getManager();

    $entities = $em->getRepository('GushhCoreBundle:SliderImage')->findAll();

    return [
      'entities' => $entities,
    ];

  }
  
  /**
   * Displays a form to edit an existing SliderImage entity.
   *
   * @Route("/{id}/edit/", name="backend_configuration_slider_image_edit")
   * @Method("GET")
   * @Template()
   */
  public function editAction($id)
  {

    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('GushhCoreBundle:SliderImage')->find($id);

    if (!$entity) {
      throw $this->createNotFoundException('Unable to find Slider entity.');
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
  * Creates a form to edit a Slider entity.
  *
  * @param SliderImage $entity The entity
  *
  * @return \Symfony\Component\Form\Form The form
  */
  private function createEditForm(SliderImage $entity)
  {

    $form = $this->createForm(new SliderImageType(), $entity, [
        'action' => $this->generateUrl('backend_configuration_slider_image_update', ['id' => $entity->getId()]),
        'attr' => [
          'id'            => 'sliderImageForm',
          'autocomplete'  => 'off'
        ],
        'method' => 'PUT',
    ]);

    return $form;

  }

  /**
   * Edits an existing SliderImage entity.
   *
   * @Route("/{id}/", name="backend_configuration_slider_image_update")
   * @Method("PUT")
   * @Template("GushhBackendBundle:ConfigurationSlider:edit.html.twig")
   */
  public function updateAction(Request $request, $id)
  {

    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('GushhCoreBundle:SliderImage')->find($id);

    if (!$entity) {
        throw $this->createNotFoundException('Unable to find Slider entity.');
    }

    $deleteForm = $this->createDeleteForm($id);
    $editForm = $this->createEditForm($entity);
    $editForm->handleRequest($request);

    if ($editForm->isValid()) {
        $em->flush();
        return $this->redirect($this->generateUrl('backend_configuration_slider_images'));
    }

    return [
      'entity'      => $entity,
      'editForm'    => $editForm->createView(),
      'deleteForm'  => $deleteForm->createView(),
    ];

  }

  /**
   * Deletes a SliderImage entity.
   *
   * @Route("/{id}/", name="backend_configuration_slider_image_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, $id)
  {

    $form = $this->createDeleteForm($id);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository('GushhCoreBundle:SliderImage')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Slider entity.');
      }

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('backend_configuration_slider_images'));

  }

  /**
   * Creates a form to delete a Slider entity by id.
   *
   * @param mixed $id The entity id
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm($id)
  {
      
    return $this->createFormBuilder(null, ['attr' => ['id' => 'sliderImageDeleteForm']])
                ->setAction($this->generateUrl('backend_configuration_slider_image_delete', ['id' => $id]))
                ->setMethod('DELETE')
                ->getForm();

  }
}
