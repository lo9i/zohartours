<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\Invoice;
use Gushh\CoreBundle\Entity\InvoiceItem;
use Gushh\CoreBundle\Form\InvoiceType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Invoice controller.
 *
 * @Route("/dashboard/invoice")
 */
class InvoiceController extends Controller
{

  /**
   * Lists all Invoices entities.
   *
   * @Route("s/", name="backend_invoices")
   * @Method("GET")
   * @Template()
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();
    $entities = $em->getRepository('GushhCoreBundle:Invoice')->findAll();

    return [
      'entities' => $entities,
    ];

  }

  /**
   * Creates a new Invoice entity.
   *
   * @Route("/", name="backend_invoice_create")
   * @Method("POST")
   * @Template("GushhBackendBundle:Invoice:new.html.twig")
   */
  public function createAction(Request $request)
  {
    $entity = new Invoice();
    $form = $this->createCreateForm($entity);
    $form->handleRequest($request);

    if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $total = 0;
        foreach ($entity->getItems() as $item) {
            $total += $item->getAmount();
        }
        $entity->setTotal($total);
        $em->persist($entity);

        foreach ($entity->getItems() as $item) {
            $item->setInvoice($entity);
            $em->persist($item);
        }
        $em->flush();

      return $this->redirect($this->generateUrl('backend_invoices'));
    }

    return [
      'entity' => $entity,
      'form'   => $form->createView(),
    ];

  }

  /**
   * Creates a form to create a Invoice entity.
   *
   * @param Invoice $entity The entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createCreateForm(Invoice $entity)
  {
    $form = $this->createForm(new InvoiceType(), $entity, [
      'action' => $this->generateUrl('backend_invoice_create'),
      'attr' => [
        'id'            => 'invoiceForm',
        'autocomplete'  => 'off'
      ],
      'method' => 'POST',
    ]);

    return $form;
  }

  /**
   * Displays a form to create a new Invoice entity.
   *
   * @Route("/new/", name="backend_invoice_new")
   * @Method("GET")
   * @Template()
   */
  public function newAction()
  {
    $entity = new Invoice();
    $form   = $this->createCreateForm($entity);

    return [
      'entity' => $entity,
      'form'   => $form->createView(),
    ];
  }

  /**
   * Finds and displays a Invoice entity.
   *
   * @Route("/{id}/", name="backend_invoice_show")
   * @Method("GET")
   * @Template()
   */
  public function showAction($id)
  {

    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('GushhCoreBundle:Invoice')->find($id);

    if (!$entity)
      throw $this->createNotFoundException('Unable to find Invoice entity.');

    $deleteForm = $this->createDeleteForm($id);

    return [
      'entity'      => $entity,
      'delete_form' => $deleteForm->createView(),
    ];

  }

    /**
     * Finds and displays a Invoice entity.
     *
     * @Route("download/{id}/", name="backend_invoice_download")
     * @Method("GET")
     * @Template()
     */
    public function downloadAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Invoice')->find($id);

        if (!$entity)
            throw $this->createNotFoundException('Unable to find Invoice entity.');

        $pdfBuilder = $this->get('knp_snappy.pdf');

        $html = $this->renderView('GushhBackendBundle:Invoice:download.html.twig',
            [
                'entity' => $entity,
            ]);

        $filename = 'Invoice-' . $entity->getId() . '.pdf';
        $response = new Response($pdfBuilder->getOutputFromHtml($html));
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename
        );

        return $response;
    }
  /**
   * Displays a form to edit an existing Invoice entity.
   *
   * @Route("/{id}/edit/", name="backend_invoice_edit")
   * @Method("GET")
   * @Template()
   */
  public function editAction($id)
  {

    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('GushhCoreBundle:Invoice')->find($id);

    if (!$entity)
      throw $this->createNotFoundException('Unable to find Invoice entity.');

    $editForm = $this->createEditForm($entity);
    $deleteForm = $this->createDeleteForm($id);

    return [
      'entity'      => $entity,
      'form'        => $editForm->createView(),
      'deleteForm'  => $deleteForm->createView(),
    ];

  }

  /**
  * Creates a form to edit a Invoice entity.
  *
  * @param Invoice $entity The entity
  *
  * @return \Symfony\Component\Form\Form The form
  */
  private function createEditForm(Invoice $entity)
  {

    $form = $this->createForm(new InvoiceType(), $entity, [
      'action' => $this->generateUrl('backend_invoice_update', ['id' => $entity->getId()]),
      'attr' => [
        'id'            => 'invoiceForm',
        'autocomplete'  => 'off'
      ],
      'method' => 'PUT',
    ]);

    return $form;

  }

  /**
   * Edits an existing Invoice entity.
   *
   * @Route("/{id}/", name="backend_invoice_update")
   * @Method("PUT")
   * @Template("GushhBackendBundle:Invoice:edit.html.twig")
   */
  public function updateAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();
    $entity = $em->getRepository('GushhCoreBundle:Invoice')->find($id);

    if (!$entity)
      throw $this->createNotFoundException('Unable to find Invoice entity.');

      $originalItems = new ArrayCollection();
      foreach ($entity->getItems() as $item) {
          $originalItems->add($item);
      }

      $deleteForm = $this->createDeleteForm($id);
      $editForm = $this->createEditForm($entity);
      $editForm->handleRequest($request);

    if ($editForm->isValid()) {
        foreach ($originalItems as $item) {
            if (false === $entity->getItems()->contains($item)) {
                // remove the Task from the Tag
//                $entity->getItems()->removeElement($item);

                // if it was a many-to-one relationship, remove the relationship like this
//                $item->setInvoice(null);

//                $em->persist($item);

                // if you wanted to delete the Tag entirely, you can also do that
                $em->remove($item);
            }
        }

        foreach ($entity->getItems() as $item) {
            if (false === $originalItems->contains($item)) {

                $item->setInvoice($entity);

                $em->persist($item);
            }
        }

        $total = 0;
        foreach ($entity->getItems() as $item) {
            $total += $item->getAmount();
            if( !$item->getInvoice() ) {
                $item->setInvoice($entity);
                $em->persist($item);
            }
        }
        $entity->setTotal($total);

      $em->flush();
      return $this->redirect($this->generateUrl('backend_invoices'));
    }

    return [
      'entity'      => $entity,
      'edit_form'   => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ];

  }

  /**
   * Deletes a Invoice entity.
   *
   * @Route("/{id}/", name="backend_invoice_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, $id)
  {
    $form = $this->createDeleteForm($id);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository('GushhCoreBundle:Invoice')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Invoice entity.');
      }

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('backend_invoices'));
  }

  /**
   * Creates a form to delete a Invoice entity by id.
   *
   * @param mixed $id The entity id
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm($id)
  {
    return $this->createFormBuilder(null, ['attr' => ['id' => 'invoiceDeleteForm']])
                ->setAction($this->generateUrl('backend_invoice_delete', ['id' => $id]))
                ->setMethod('DELETE')
                ->getForm();

  }
}
