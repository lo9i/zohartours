<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\HotelCurrency;
use Gushh\CoreBundle\Entity\CancellationPolicy;
use Gushh\CoreBundle\Form\HotelCurrencyType;

/**
 * HotelCurrency controller.
 *
 * @Route("/dashboard/hotel-currenc")
 */
class HotelCurrencyController extends Controller
{

    /**
     * Lists all HotelCurrency entities.
     *
     * @Route("ies/", name="backend_hotel_currencies")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GushhCoreBundle:Hotel')->findAllHotels($this->getUser()->getVip());

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new HotelCurrency entity.
     *
     * @Route("y/", name="backend_hotel_currency_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:HotelCurrency:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new HotelCurrency();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_hotel_currencies'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a HotelCurrency entity.
     *
     * @param HotelCurrency $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(HotelCurrency $entity)
    {
        $form = $this->createForm(new HotelCurrencyType(), $entity, array(
            'action' => $this->generateUrl('backend_hotel_currency_create'),
            'attr' => array(
                'id'    => 'hotelCurrencyForm'
                ),
            'method' => 'POST',
        ));

        // $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new HotelCurrency entity.
     *
     * @Route("y/new/", name="backend_hotel_currency_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new HotelCurrency();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a HotelCurrency entity.
     *
     * @Route("y/{hcid}/", name="backend_hotel_currency_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($hcid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:HotelCurrency')->find($hcid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HotelCurrency entity.');
        }

        $deleteForm = $this->createDeleteForm($hcid);

        return array(
            'entity'      => $entity,
            'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing HotelCurrency entity.
     *
     * @Route("y/{hcid}/edit/", name="backend_hotel_currency_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($hcid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:HotelCurrency')->find($hcid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HotelCurrency entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($hcid);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a HotelCurrency entity.
    *
    * @param HotelCurrency $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(HotelCurrency $entity)
    {
        $form = $this->createForm(new HotelCurrencyType(), $entity, array(
            'action' => $this->generateUrl('backend_hotel_currency_update', array('hcid' => $entity->getId())),
            'attr' => array(
                'id'    => 'hotelCurrencyForm'
                ),
            'method' => 'PUT',
        ));

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing HotelCurrency entity.
     *
     * @Route("y/{hcid}/", name="backend_hotel_currency_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:HotelCurrency:edit.html.twig")
     */
    public function updateAction(Request $request, $hcid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:HotelCurrency')->find($hcid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HotelCurrency entity.');
        }

        $deleteForm = $this->createDeleteForm($hcid);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('backend_hotel_currencies'));
        }

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'deleteForm'  => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a HotelCurrency entity.
     *
     * @Route("y/{hcid}/", name="backend_hotel_currency_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $hcid)
    {
        $form = $this->createDeleteForm($hcid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:HotelCurrency')->find($hcid);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find HotelCurrency entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_hotel_currencies'));
    }

    /**
     * Creates a form to delete a HotelCurrency entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($hcid)
    {
        return $this->createFormBuilder(null, array('attr' => ['id' => 'hotelCurrencyDeleteForm']))
            ->setAction($this->generateUrl('backend_hotel_currency_delete', array('hcid' => $hcid)))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

}
