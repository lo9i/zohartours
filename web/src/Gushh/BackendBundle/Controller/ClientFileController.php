<?php

namespace Gushh\BackendBundle\Controller;

use Gushh\CoreBundle\Entity\ClientFileCommissionPaid;
use Gushh\CoreBundle\Form\ClientFileCommissionPaidType;
use Gushh\CoreBundle\Entity\ClientFilePaymentReceived;
use Gushh\CoreBundle\Form\ClientFilePaymentReceivedType;
use Gushh\CoreBundle\Entity\ClientFilePaymentMade;
use Gushh\CoreBundle\Form\ClientFilePaymentMadeType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\ClientFile;
use Gushh\CoreBundle\Entity\ClientFileItem;
use Gushh\CoreBundle\Form\ClientFileType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * ClientFile controller.
 *
 * @Route("/dashboard/client_file")
 */
class ClientFileController extends Controller
{

    /**
     * Lists all ClientFiles entities.
     *
     * @Route("s/", name="backend_client_files")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('GushhCoreBundle:ClientFile')->findAll();

        return [
            'entities' => $entities,
        ];

    }

    /**
     * Creates a new ClientFile entity.
     *
     * @Route("/", name="backend_client_file_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:ClientFile:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ClientFile();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $total_net = 0;
            $total_sale = 0;
            $total_commission = 0;
            foreach ($entity->getItems() as $item) {
                $total_net += $item->getAmountNet();
                $total_sale += $item->getAmountSale();
                if ($item->getAmountCommission() == null)
                    $item->setAmountCommission(0);

                $total_commission += $item->getAmountCommission();
            }
            $entity->setTotalNet($total_net);
            $entity->setTotalSale($total_sale);
            $entity->setTotalCommission($total_commission);
            $em->persist($entity);

            $status = $em->getRepository('GushhCoreBundle:ClientFileItemStatus')->findOneById(1);

            foreach ($entity->getItems() as $item) {
                $item->setClientFile($entity);
                $item->setStatus($status);
                $em->persist($item);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('backend_client_files'));
        }

        return [
            'entity' => $entity,
            'form' => $form->createView(),
        ];

    }

    /**
     * Creates a form to create a ClientFile entity.
     *
     * @param ClientFile $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ClientFile $entity)
    {
        $form = $this->createForm(new ClientFileType(), $entity, [
            'action' => $this->generateUrl('backend_client_file_create'),
            'attr' => [
                'id' => 'clientFileForm',
                'autocomplete' => 'off'
            ],
            'method' => 'POST',
        ]);

        return $form;
    }

    /**
     * Displays a form to create a new ClientFile entity.
     *
     * @Route("/new/", name="backend_client_file_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ClientFile();
        $form = $this->createCreateForm($entity);

        return [
            'entity' => $entity,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a ClientFile entity.
     *
     * @Route("/{id}/", name="backend_client_file_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:ClientFile')->find($id);

        if (!$entity)
            throw $this->createNotFoundException('Unable to find ClientFile entity.');

        $deleteForm = $this->createDeleteForm($id);

        $entity_payment_made = new ClientFilePaymentMade();
        $entity_payment_received = new ClientFilePaymentReceived();
        $entity_commission_paid = new ClientFileCommissionPaid();

        $paymentMadeForm = $this->createPaymentMadeForm($id, $entity_payment_made);
        $paymentReceivedForm = $this->createPaymentReceivedForm($id, $entity_payment_received);
        $commissionPaidForm = $this->createCommissionPaidForm($id, $entity_commission_paid);

        return [
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'payment_made_form' => $paymentMadeForm->createView(),
            'payment_received_form' => $paymentReceivedForm->createView(),
            'commission_paid_form' => $commissionPaidForm->createView()
        ];

    }

    /**
     * Creates a form to create a payment made entity.
     *
     * @param ClientFilePaymentMade $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createPaymentMadeForm($cfid, $entity)
    {
        $form = $this->createForm(new ClientFilePaymentMadeType(), $entity, [
            'action' => $this->generateUrl('backend_client_file_payment_made', ['id' => $cfid]),
            'attr' => [
                'id' => 'clientFilePaymentMadeForm',
                'autocomplete' => 'off'
            ],
            'method' => 'PUT',
        ]);

        return $form;
    }
    /**
     * Creates a form to create a payment made entity.
     *
     * @param ClientFilePaymentReceived $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createPaymentReceivedForm($cfid, $entity)
    {
        $form = $this->createForm(new ClientFilePaymentReceivedType(), $entity, [
            'action' => $this->generateUrl('backend_client_file_payment_received', ['id' => $cfid]),
            'attr' => [
                'id' => 'clientFilePaymentReceivedForm',
                'autocomplete' => 'off'
            ],
            'method' => 'PUT',
        ]);

        return $form;
    }

    /**
     * Creates a form to create a payment made entity.
     *
     * @param ClientFileCommissionPaid $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCommissionPaidForm($cfid, $entity)
    {
        $form = $this->createForm(new ClientFileCommissionPaidType(), $entity, [
            'action' => $this->generateUrl('backend_client_file_commission_paid', ['id' => $cfid]),
            'attr' => [
                'id' => 'clientFileCommissionPaidForm',
                'autocomplete' => 'off'
            ],
            'method' => 'PUT',
        ]);

        return $form;
    }

    /**
     * Edits an existing ClientFile entity.
     *
     * @Route("/{id}/", name="backend_client_file_payment_received")
     * @Method("PUT")
     * @Template("GushhBackendBundle:ClientFilePaymentReceived:new.html.twig")
     */
    public function addPaymentReceivedAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new ClientFilePaymentReceived();
        $client_file = $em->getRepository('GushhCoreBundle:ClientFile')->find($id);

        if (!$client_file)
            throw $this->createNotFoundException('Unable to find ClientFile entity.');

        $form = $this->createPaymentReceivedForm($id, $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setClientFile($client_file);
            $em->persist($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('backend_client_file_show', ['id' => $id]));
    }

    /**
     * Edits an existing ClientFile entity.
     *
     * @Route("/{id}/", name="backend_client_file_commission_paid")
     * @Method("PUT")
     * @Template("GushhBackendBundle:ClientFileCommissionPaid:new.html.twig")
     */
    public function addCommissionPaidAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new ClientFileCommissionPaid();
        $client_file = $em->getRepository('GushhCoreBundle:ClientFile')->find($id);

        if (!$client_file)
            throw $this->createNotFoundException('Unable to find ClientFile entity.');

        $form = $this->createCommissionPaidForm($id, $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setClientFile($client_file);
            $em->persist($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('backend_client_file_show', ['id' => $id]));
    }

    /**
     * Edits an existing ClientFile entity.
     *
     * @Route("/{id}/", name="backend_client_file_payment_made")
     * @Method("PUT")
     * @Template("GushhBackendBundle:ClientFilePaymentMade:new.html.twig")
     */
    public function addPaymentMadeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new ClientFilePaymentMade();
        $client_file = $em->getRepository('GushhCoreBundle:ClientFile')->find($id);

        if (!$client_file)
            throw $this->createNotFoundException('Unable to find ClientFile entity.');

        $form = $this->createPaymentMadeForm($id, $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setClientFile($client_file);
            $em->persist($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('backend_client_file_show', ['id' => $id]));
    }

    /**
     * Edits an existing ClientFile entity.
     *
     * @Route("/{id}/", name="backend_client_file_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:ClientFile:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('GushhCoreBundle:ClientFile')->find($id);

        if (!$entity)
            throw $this->createNotFoundException('Unable to find ClientFile entity.');

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
                    $em->remove($item);
                }
            }

            $status = $em->getRepository('GushhCoreBundle:ClientFileItemStatus')->findOneById(1);
            foreach ($entity->getItems() as $item) {
                if (false === $originalItems->contains($item)) {

                    $item->setClientFile($entity);
                    $item->setStatus($status);
                    $em->persist($item);
                }
            }

            $total_net = 0;
            $total_sale = 0;
            $total_commission = 0;
            foreach ($entity->getItems() as $item) {
                $total_net += $item->getAmountNet();
                $total_sale += $item->getAmountSale();
                if ($item->getAmountCommission() == null)
                    $item->setAmountCommission(0);

                $total_commission += $item->getAmountCommission();
                if (!$item->getClientFile()) {
                    $item->setClientFile($entity);
                    $em->persist($item);
                }
            }
            $entity->setTotalNet($total_net);
            $entity->setTotalSale($total_sale);
            $entity->setTotalCommission($total_commission);

            $em->flush();
            return $this->redirect($this->generateUrl('backend_client_files'));
        }

        return [
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];

    }

    /**
     * Finds and displays a ClientFile entity.
     *
     * @Route("download/{id}/", name="backend_client_file_download")
     * @Method("GET")
     * @Template()
     */
    public function downloadAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:ClientFile')->find($id);

        if (!$entity)
            throw $this->createNotFoundException('Unable to find ClientFile entity.');

        $pdfBuilder = $this->get('knp_snappy.pdf');

        $html = $this->renderView('GushhBackendBundle:ClientFile:download.html.twig',
            [
                'entity' => $entity,
            ]);

        $filename = 'ClientFile-' . $entity->getId() . '.pdf';
        $response = new Response($pdfBuilder->getOutputFromHtml($html));
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename
        );

        return $response;
    }

    /**
     * Displays a form to edit an existing ClientFile entity.
     *
     * @Route("/{id}/edit/", name="backend_client_file_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:ClientFile')->find($id);

        if (!$entity)
            throw $this->createNotFoundException('Unable to find ClientFile entity.');

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return [
            'entity' => $entity,
            'form' => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        ];

    }

    /**
     * Creates a form to edit a ClientFile entity.
     *
     * @param ClientFile $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ClientFile $entity)
    {

        $form = $this->createForm(new ClientFileType(), $entity, [
            'action' => $this->generateUrl('backend_client_file_update', ['id' => $entity->getId()]),
            'attr' => [
                'id' => 'clientFileForm',
                'autocomplete' => 'off'
            ],
            'method' => 'PUT',
        ]);

        return $form;

    }

    /**
     * Deletes a ClientFile entity.
     *
     * @Route("/{id}/", name="backend_client_file_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:ClientFile')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ClientFile entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_client_files'));
    }

    /**
     * @Route("/{id}/cancel/{iid}", name="backend_client_file_cancel")
     * @Method("POST")
     */
    public function cancelAction($id, $iid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:ClientFileItem')->find($iid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Date entity.');
        }

        $status = $em->getRepository('GushhCoreBundle:ClientFileItemStatus')->findOneById(1);
        $entity->setStatus($status);

        $em->persist($entity);
        $em->flush();

        return new Response('Success');
    }


    /**
     * Creates a form to delete a ClientFile entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(null, ['attr' => ['id' => 'clientFileDeleteForm']])
            ->setAction($this->generateUrl('backend_client_file_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->getForm();

    }
}
