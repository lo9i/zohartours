<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\Hotel;
use Gushh\CoreBundle\Entity\City;
use Gushh\CoreBundle\Entity\CityImage;
use Gushh\CoreBundle\Entity\CancellationPolicy;
use Gushh\CoreBundle\Form\HotelType;
use Gushh\CoreBundle\Form\EditHotelType;

/**
 * Hotel controller.
 *
 * @Route("/dashboard/hotel")
 */
class HotelController extends Controller
{

    /**
     * Lists all Hotel entities.
     *
     * @Route("s/", name="backend_hotels")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GushhCoreBundle:Hotel')->findAllHotels($this->getUser()->getVip());

        return [
            'entities' => $entities,
        ];
    }

    /**
     * Creates a new Hotel entity.
     *
     * @Route("/", name="backend_hotel_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:Hotel:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Hotel();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($request->get('stars')) {
                $entity->setStars($request->get('stars'));
            }

            $hotelCity = $entity->getCity();
            $slug = strtolower(str_replace(' ', '-', $hotelCity));
            $city = $em->getRepository('GushhCoreBundle:City')->findOneBySlug($slug);

            if (!$city) {
                $newCity = new City();
                $newCity->setName($hotelCity);
                
                $newCityImage = new CityImage();
                $newCityImage->setPath('default.png');
                $newCityImage->setCity($newCity);
                
                
                $entity->setCityImage($newCity);

                $em->persist($newCityImage);
                $em->persist($newCity);               
                
            } else {
                $entity->setCityImage($city);
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect( $this->generateUrl('backend_hotel_show', ['hid' => $entity->getId()]));
        }

        return [
                'entity' => $entity,
                'form'   => $form->createView(),
		'errors' => $form->getErrors(true)
            ];
    }

    /**
     * Creates a form to create a Hotel entity.
     *
     * @param Hotel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Hotel $entity)
    {
        $form = $this->createForm(new HotelType(), $entity, [
            'action' => $this->generateUrl('backend_hotel_create'),
            'attr' => [
                'id'    => 'hotelForm',
                'autocomplete' => 'off'
            ],
            'method' => 'POST',
        ]);

        // $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Hotel entity.
     *
     * @Route("/new/", name="backend_hotel_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Hotel();
        $form   = $this->createCreateForm($entity);

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Hotel entity.
     *
     * @Route("/{hid}/", name="backend_hotel_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($hid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Hotel')->find($hid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Hotel entity.');
        }

        $deleteForm = $this->createDeleteForm($hid);

        return [
            'entity'     => $entity,
            'deleteForm' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing Hotel entity.
     *
     * @Route("/{hid}/edit/", name="backend_hotel_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($hid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Hotel')->find($hid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Hotel entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($hid);

        return [
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        ];
    }

    /**
    * Creates a form to edit a Hotel entity.
    *
    * @param Hotel $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Hotel $entity)
    {
        $form = $this->createForm(new EditHotelType(), $entity, [
            'action' => $this->generateUrl('backend_hotel_update', array('hid' => $entity->getId())),
            'attr' => [
                'id'    => 'hotelForm',
                'autocomplete' => 'off'
            ],
            'method' => 'PUT',
        ]);

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Hotel entity.
     *
     * @Route("/{hid}/", name="backend_hotel_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:Hotel:edit.html.twig")
     */
    public function updateAction(Request $request, $hid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:Hotel')->find($hid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Hotel entity.');
        }

        $deleteForm = $this->createDeleteForm($hid);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if ($request->get('stars')) {
                $entity->setStars($request->get('stars'));
            }

            $hotelCity = $entity->getCity();
            $slug = strtolower(str_replace(' ', '-', $hotelCity));
            $city = $em->getRepository('GushhCoreBundle:City')->findOneBySlug($slug);

            if (!$city) {
                $newCity = new City();
                $newCity->setName($hotelCity);
                
                $newCityImage = new CityImage();
                $newCityImage->setPath('default.png');
                $newCityImage->setCity($newCity);
                
                
                $entity->setCityImage($newCity);

                $em->persist($newCityImage);
                $em->persist($newCity);               
                
            } else {
                $entity->setCityImage($city);
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_hotel_show', array('hid' => $entity->getId())));
        }

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a Hotel entity.
     *
     * @Route("/{hid}/", name="backend_hotel_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $hid)
    {
        $form = $this->createDeleteForm($hid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:Hotel')->find($hid);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Hotel entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_hotels'));
    }

    /**
     * Creates a form to delete a Hotel entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($hid)
    {
        return $this->createFormBuilder(null, array('attr' => ['id' => 'hotelDeleteForm']))
            ->setAction($this->generateUrl('backend_hotel_delete', array('hid' => $hid)))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

}
