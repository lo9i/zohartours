<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\Amenity;
use Gushh\CoreBundle\Entity\CancellationPolicy;
use Gushh\CoreBundle\Form\AmenityType;

/**
 * HotelAmenity controller.
 *
 * @Route("/dashboard/hotel-amenit")
 */
class HotelAmenityController extends Controller
{

    /**
     * Lists all Amenity entities.
     *
     * @Route("ies/", name="backend_hotel_amenities")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GushhCoreBundle:Amenity')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Amenity entity.
     *
     * @Route("y/", name="backend_hotel_amenity_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:HotelAmenity:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Amenity();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_hotel_amenities'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Amenity entity.
     *
     * @param Amenity $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Amenity $entity)
    {
        $form = $this->createForm(new AmenityType(), $entity, array(
            'action' => $this->generateUrl('backend_hotel_amenity_create'),
            'attr' => array(
                'id'    => 'hotelAmenityForm'
                ),
            'method' => 'POST',
        ));

        // $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Amenity entity.
     *
     * @Route("y/new/", name="backend_hotel_amenity_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Amenity();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Amenity entity.
     *
     * @Route("y/{haid}/", name="backend_hotel_amenity_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($haid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Amenity')->find($haid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Amenity entity.');
        }

        $deleteForm = $this->createDeleteForm($haid);

        return array(
            'entity'      => $entity,
            'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Amenity entity.
     *
     * @Route("y/{haid}/edit/", name="backend_hotel_amenity_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($haid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Amenity')->find($haid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Amenity entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($haid);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Amenity entity.
    *
    * @param Amenity $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Amenity $entity)
    {
        $form = $this->createForm(new AmenityType(), $entity, array(
            'action' => $this->generateUrl('backend_hotel_amenity_update', array('haid' => $entity->getId())),
            'attr' => array(
                'id'    => 'hotelAmenityForm'
                ),
            'method' => 'PUT',
        ));

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Amenity entity.
     *
     * @Route("y/{haid}/", name="backend_hotel_amenity_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:HotelAmenity:edit.html.twig")
     */
    public function updateAction(Request $request, $haid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Amenity')->find($haid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Amenity entity.');
        }

        $deleteForm = $this->createDeleteForm($haid);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('backend_hotel_amenities'));
        }

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'deleteForm'  => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Amenity entity.
     *
     * @Route("y/{haid}/", name="backend_hotel_amenity_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $haid)
    {
        $form = $this->createDeleteForm($haid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:Amenity')->find($haid);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Amenity entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_hotel_amenities'));
    }

    /**
     * Creates a form to delete a Amenity entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($haid)
    {
        return $this->createFormBuilder(null, array('attr' => ['id' => 'hotelAmenityDeleteForm']))
            ->setAction($this->generateUrl('backend_hotel_amenity_delete', array('haid' => $haid)))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

}
