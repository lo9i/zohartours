<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\Provider;
use Gushh\CoreBundle\Entity\ProviderItem;
use Gushh\CoreBundle\Form\ProviderType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Provider controller.
 *
 * @Route("/dashboard/provider")
 */
class ProviderController extends Controller
{

    /**
     * Lists all Providers entities.
     *
     * @Route("s/", name="backend_providers")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('GushhCoreBundle:Provider')->findAll();

        return [
            'entities' => $entities,
        ];

    }

    /**
     * Creates a new Provider entity.
     *
     * @Route("/", name="backend_provider_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:Provider:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Provider();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('backend_providers'));
        }

        return [
            'entity' => $entity,
            'form' => $form->createView(),
        ];

    }

    /**
     * Creates a form to create a Provider entity.
     *
     * @param Provider $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Provider $entity)
    {
        $form = $this->createForm(new ProviderType(), $entity, [
            'action' => $this->generateUrl('backend_provider_create'),
            'attr' => [
                'id' => 'providerForm',
                'autocomplete' => 'off'
            ],
            'method' => 'POST',
        ]);

        return $form;
    }

    /**
     * Displays a form to create a new Provider entity.
     *
     * @Route("/new/", name="backend_provider_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Provider();
        $form = $this->createCreateForm($entity);

        return [
            'entity' => $entity,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Provider entity.
     *
     * @Route("/{id}/", name="backend_provider_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Provider')->find($id);

        if (!$entity)
            throw $this->createNotFoundException('Unable to find Provider entity.');

        $deleteForm = $this->createDeleteForm($id);

        return [
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ];

    }

    /**
     * Displays a form to edit an existing Provider entity.
     *
     * @Route("/{id}/edit/", name="backend_provider_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Provider')->find($id);

        if (!$entity)
            throw $this->createNotFoundException('Unable to find Provider entity.');

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return [
            'entity' => $entity,
            'form' => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        ];

    }

    /**
     * Creates a form to edit a Provider entity.
     *
     * @param Provider $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Provider $entity)
    {

        $form = $this->createForm(new ProviderType(), $entity, [
            'action' => $this->generateUrl('backend_provider_update', ['id' => $entity->getId()]),
            'attr' => [
                'id' => 'providerForm',
                'autocomplete' => 'off'
            ],
            'method' => 'PUT',
        ]);

        return $form;

    }

    /**
     * Edits an existing Provider entity.
     *
     * @Route("/{id}/", name="backend_provider_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:Provider:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('GushhCoreBundle:Provider')->find($id);

        if (!$entity)
            throw $this->createNotFoundException('Unable to find Provider entity.');

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('backend_providers'));
        }

        return [
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];

    }

    /**
     * Deletes a Provider entity.
     *
     * @Route("/{id}/", name="backend_provider_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:Provider')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Provider entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_providers'));
    }

    /**
     * Creates a form to delete a Provider entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(null, ['attr' => ['id' => 'providerDeleteForm']])
            ->setAction($this->generateUrl('backend_provider_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->getForm();

    }
}
