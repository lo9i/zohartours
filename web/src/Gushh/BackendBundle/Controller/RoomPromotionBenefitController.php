<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gushh\CoreBundle\Entity\Promotion;
use Gushh\CoreBundle\Entity\PromotionBenefit;

use Gushh\CoreBundle\Form\PromotionBenefitNightDiscountType;
use Gushh\CoreBundle\Form\PromotionBenefitStayDiscountType;
use Gushh\CoreBundle\Form\PromotionBenefitRoomServiceDiscountType;
use Gushh\CoreBundle\Form\PromotionBenefitHotelServiceDiscountType;

/**
 * RoomPromotionBenefit controller.
 *
 * @Route("/dashboard/hotel")
 */
class RoomPromotionBenefitController extends Controller
{

    // NIGHT DISCOUNTS

    /**
     * Creates a new PromotionBenefit entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/night-discount/", name="backend_room_promotion_benefit_night_discount_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:RoomPromotionBenefit:newNightDiscount.html.twig")
     */
    public function createNightDiscountAction(Request $request, $hid, $rid, $pid)
    {
        $entity = new PromotionBenefit();
        $em = $this->getDoctrine()->getManager();
        $promotion = $em->getRepository('GushhCoreBundle:Promotion')->findOneById($pid);
        
        $form = $this->createCreateForm($entity, $hid, $rid, $pid, 'NIGHT-DISCOUNT');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $benefitType = $em->getRepository('GushhCoreBundle:PromotionBenefitType')->findOneBySlug('night-discount');
            $entity->setType($benefitType);
            $entity->setPromotion($promotion);

            if (!$entity->getHasLimit()) :
                $entity->setLimitValue(1);
            endif;

            // echo "<pre>";
            // var_dump($entity);
            // die;

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'tab' => 'benefits']));
        }

        return [
            'entity'    => $entity,
            'promotion' => $promotion,
            'form'      => $form->createView(),
        ];
    }

    /**
     * Displays a form to create a new Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/night-discount/new/", name="backend_room_promotion_benefit_night_discount_new")
     * @Method("GET")
     * @Template()
     */
    public function newNightDiscountAction($hid, $rid, $pid)
    {
        $entity = new PromotionBenefit();
        $form   = $this->createCreateForm($entity, $hid, $rid, $pid, 'NIGHT-DISCOUNT');

        $em = $this->getDoctrine()->getManager();
        $promotion = $em->getRepository('GushhCoreBundle:Promotion')->find($pid);

        return [
            'promotion' => $promotion,
            'entity'    => $entity,
            'form'      => $form->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing PromotionBenefit entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/night-discount/{bid}/edit/", name="backend_room_promotion_benefit_night_discount_edit")
     * @Method("GET")
     * @Template()
     */
    public function editNightDiscountAction($hid, $rid, $pid, $bid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:PromotionBenefit')->find($bid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PromotionBenefit entity.');
        }

        $editForm = $this->createEditForm($entity, $hid, $rid, $pid, $bid, 'NIGHT-DISCOUNT');
        $deleteForm = $this->createDeleteForm($hid, $rid, $pid, $bid);

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'deleteForm'  => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/night-discount/{bid}/", name="backend_room_promotion_benefit_night_discount_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:RoomPromotionBenefit:editNightDiscount.html.twig")
     */
    public function updateNightDiscountAction(Request $request, $hid, $rid, $pid, $bid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:PromotionBenefit')->find($bid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PromotionBenefit entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid, $pid, $bid);
        $editForm = $this->createEditForm($entity, $hid, $rid, $pid, $bid, 'NIGHT-DISCOUNT');
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if (!$entity->getHasLimit()) :
                $entity->setLimitValue(1);
                $em->persist($entity);
            endif;
            
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'tab' => 'benefits']));
        }

        return [
            'entity'        => $entity,
            'form'          => $editForm->createView(),
            'deleteForm'    => $deleteForm->createView(),
        ];
    }


    // STAY DISCOUNT
    
    /**
     * Creates a new PromotionBenefit entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/stay-discount/", name="backend_room_promotion_benefit_stay_discount_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:RoomPromotionBenefit:newStayDiscount.html.twig")
     */
    public function createStayDiscountAction(Request $request, $hid, $rid, $pid)
    {
        $entity = new PromotionBenefit();
        $em = $this->getDoctrine()->getManager();
        $promotion = $em->getRepository('GushhCoreBundle:Promotion')->findOneById($pid);
        
        $form = $this->createCreateForm($entity, $hid, $rid, $pid, 'STAY-DISCOUNT');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $benefitType = $em->getRepository('GushhCoreBundle:PromotionBenefitType')->findOneBySlug('stay-discount');
            $entity->setType($benefitType);
            $entity->setPromotion($promotion);

            // if (!$entity->getHasLimit()) :
            //     $entity->setLimitValue(1);
            // endif;

            // echo "<pre>";
            // var_dump($entity);
            // die;

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'tab' => 'benefits']));
        }

        return [
            'entity'    => $entity,
            'promotion' => $promotion,
            'form'      => $form->createView(),
        ];
    }

    /**
     * Displays a form to create a new Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/stay-discount/new/", name="backend_room_promotion_benefit_stay_discount_new")
     * @Method("GET")
     * @Template()
     */
    public function newStayDiscountAction($hid, $rid, $pid)
    {
        $entity = new PromotionBenefit();
        $form   = $this->createCreateForm($entity, $hid, $rid, $pid, 'STAY-DISCOUNT');

        $em = $this->getDoctrine()->getManager();
        $promotion = $em->getRepository('GushhCoreBundle:Promotion')->find($pid);

        return [
            'promotion' => $promotion,
            'entity'    => $entity,
            'form'      => $form->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing PromotionBenefit entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/stay-discount/{bid}/edit/", name="backend_room_promotion_benefit_stay_discount_edit")
     * @Method("GET")
     * @Template()
     */
    public function editStayDiscountAction($hid, $rid, $pid, $bid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:PromotionBenefit')->find($bid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PromotionBenefit entity.');
        }

        $editForm = $this->createEditForm($entity, $hid, $rid, $pid, $bid, 'STAY-DISCOUNT');
        $deleteForm = $this->createDeleteForm($hid, $rid, $pid, $bid);

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'deleteForm'  => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/stay-discount/{bid}/", name="backend_room_promotion_benefit_stay_discount_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:RoomPromotionBenefit:editStayDiscount.html.twig")
     */
    public function updateStayDiscountAction(Request $request, $hid, $rid, $pid, $bid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:PromotionBenefit')->find($bid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PromotionBenefit entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid, $pid, $bid);
        $editForm = $this->createEditForm($entity, $hid, $rid, $pid, $bid, 'STAY-DISCOUNT');
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if (!$entity->getHasLimit()) :
                $entity->setLimitValue(1);
                $em->persist($entity);
            endif;
            
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'tab' => 'benefits']));
        }

        return [
            'entity'        => $entity,
            'form'          => $editForm->createView(),
            'deleteForm'    => $deleteForm->createView(),
        ];
    }

    // ROOM SERVICES DISCOUNTS
    
    /**
     * Creates a new PromotionBenefit entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/room-service-discount/", name="backend_room_promotion_benefit_room_service_discount_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:RoomPromotionBenefit:newRoomServiceDiscount.html.twig")
     */
    public function createRoomServiceDiscountAction(Request $request, $hid, $rid, $pid)
    {

        $em = $this->getDoctrine()->getManager();
        $promotion  = $em->getRepository('GushhCoreBundle:Promotion')->findOneById($pid);
        $type       = $em->getRepository('GushhCoreBundle:PromotionBenefitType')->findOneByGroup('room-services');

        $entity = new PromotionBenefit();
        $entity->setPromotion($promotion);
        $entity->setType($type);

        $form = $this->createCreateForm($entity, $hid, $rid, $pid, 'ROOM-SERVICE-DISCOUNT');
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'tab' => 'benefits']));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );

    }

    /**
     * Displays a form to create a new Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/room-service-discount/new/", name="backend_room_promotion_benefit_room_service_discount_new")
     * @Method("GET")
     * @Template()
     */
    public function newRoomServiceDiscountAction($hid, $rid, $pid)
    {
        
        $em = $this->getDoctrine()->getManager();
        $promotion = $em->getRepository('GushhCoreBundle:Promotion')->findOneById($pid);

        $entity = new PromotionBenefit();
        $entity->setPromotion($promotion);

        $form   = $this->createCreateForm($entity, $hid, $rid, $pid, 'ROOM-SERVICE-DISCOUNT');

        
        return array(
            'promotion'  => $promotion,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing PromotionBenefit entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/room-service-discount/{bid}/edit/", name="backend_room_promotion_benefit_room_service_discount_edit")
     * @Method("GET")
     * @Template()
     */
    public function editRoomServiceDiscountAction($hid, $rid, $pid, $bid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:PromotionBenefit')->find($bid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PromotionBenefit entity.');
        }

        $editForm = $this->createEditForm($entity, $hid, $rid, $pid, $bid, 'ROOM-SERVICE-DISCOUNT');
        $deleteForm = $this->createDeleteForm($hid, $rid, $pid, $bid);

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'deleteForm'  => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/room-service-discount/{bid}/", name="backend_room_promotion_benefit_room_service_discount_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:RoomPromotionBenefit:editRoomServiceDiscount.html.twig")
     */
    public function updateRoomServiceDiscountAction(Request $request, $hid, $rid, $pid, $bid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:PromotionBenefit')->find($bid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PromotionBenefit entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid, $pid, $bid);
        $editForm = $this->createEditForm($entity, $hid, $rid, $pid, $bid, 'ROOM-SERVICE-DISCOUNT');
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'tab' => 'benefits']));
        }

        return array(
            'entity'        => $entity,
            'form'          => $editForm->createView(),
            'deleteForm'    => $deleteForm->createView(),
        );
    }

    // HOTEL SERVICES DISCOUNTS
    
    /**
     * Creates a new PromotionBenefit entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/hotel-service-discount/", name="backend_room_promotion_benefit_hotel_service_discount_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:RoomPromotionBenefit:newHotelServiceDiscount.html.twig")
     */
    public function createHotelServiceDiscountAction(Request $request, $hid, $rid, $pid)
    {

        $em = $this->getDoctrine()->getManager();
        $promotion  = $em->getRepository('GushhCoreBundle:Promotion')->findOneById($pid);
        $type       = $em->getRepository('GushhCoreBundle:PromotionBenefitType')->findOneByGroup('hotel-services');

        $entity = new PromotionBenefit();
        $entity->setPromotion($promotion);
        $entity->setType($type);

        $form = $this->createCreateForm($entity, $hid, $rid, $pid, 'HOTEL-SERVICE-DISCOUNT');
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'tab' => 'benefits']));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );

    }

    /**
     * Displays a form to create a new Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/hotel-service-discount/new/", name="backend_room_promotion_benefit_hotel_service_discount_new")
     * @Method("GET")
     * @Template()
     */
    public function newHotelServiceDiscountAction($hid, $rid, $pid)
    {
        
        $em = $this->getDoctrine()->getManager();
        $promotion = $em->getRepository('GushhCoreBundle:Promotion')->findOneById($pid);

        $entity = new PromotionBenefit();
        $entity->setPromotion($promotion);

        $form   = $this->createCreateForm($entity, $hid, $rid, $pid, 'HOTEL-SERVICE-DISCOUNT');

        
        return array(
            'promotion'  => $promotion,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing PromotionBenefit entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/hotel-service-discount/{bid}/edit/", name="backend_room_promotion_benefit_hotel_service_discount_edit")
     * @Method("GET")
     * @Template()
     */
    public function editHotelServiceDiscountAction($hid, $rid, $pid, $bid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:PromotionBenefit')->find($bid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PromotionBenefit entity.');
        }

        $editForm = $this->createEditForm($entity, $hid, $rid, $pid, $bid, 'HOTEL-SERVICE-DISCOUNT');
        $deleteForm = $this->createDeleteForm($hid, $rid, $pid, $bid);

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'deleteForm'  => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/hotel-service-discount/{bid}/", name="backend_room_promotion_benefit_hotel_service_discount_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:RoomPromotionBenefit:editHotelServiceDiscount.html.twig")
     */
    public function updateHotelServiceDiscountAction(Request $request, $hid, $rid, $pid, $bid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:PromotionBenefit')->find($bid);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PromotionBenefit entity.');
        }

        $deleteForm = $this->createDeleteForm($hid, $rid, $pid, $bid);
        $editForm = $this->createEditForm($entity, $hid, $rid, $pid, $bid, 'HOTEL-SERVICE-DISCOUNT');
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'tab' => 'benefits']));
        }

        return array(
            'entity'        => $entity,
            'form'          => $editForm->createView(),
            'deleteForm'    => $deleteForm->createView(),
        );
    }

    // LOGIC
    
    /**
     * Deletes a Promotion entity.
     *
     * @Route("/{hid}/room/{rid}/promotion/{pid}/benefit/{bid}/", name="backend_room_promotion_benefit_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $hid, $rid, $pid, $bid)
    {
        $form = $this->createDeleteForm($hid, $rid, $pid, $bid);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:PromotionBenefit')->find($bid);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Promotion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_room_promotion_show', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'tab' => 'benefits']));
    }

    /**
     * Creates a form to create a Promotion entity.
     *
     * @param Promotion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PromotionBenefit $entity, $hid, $rid, $pid, $type)
    {

        if ($type === 'STAY-DISCOUNT') {
            $form = $this->createForm(new PromotionBenefitStayDiscountType(), $entity, array(
                'action' => $this->generateUrl('backend_room_promotion_benefit_stay_discount_create', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid]),
                'attr' => array(
                    'id'    => 'roomPromotionForm'
                    ),
                'method' => 'POST',
            ));
        } elseif ($type === 'NIGHT-DISCOUNT') {
            $form = $this->createForm(new PromotionBenefitNightDiscountType(), $entity, array(
                'action' => $this->generateUrl('backend_room_promotion_benefit_night_discount_create', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid]),
                'attr' => array(
                    'id'    => 'roomPromotionForm'
                    ),
                'method' => 'POST',
            ));            
        } elseif ($type === 'ROOM-SERVICE-DISCOUNT') {
            $form = $this->createForm(new PromotionBenefitRoomServiceDiscountType(), $entity, array(
                'action' => $this->generateUrl('backend_room_promotion_benefit_room_service_discount_create', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid]),
                'attr' => array(
                    'id'    => 'roomPromotionForm'
                    ),
                'method' => 'POST',
            ));            
        } elseif ($type === 'HOTEL-SERVICE-DISCOUNT') {
            $form = $this->createForm(new PromotionBenefitHotelServiceDiscountType(), $entity, array(
                'action' => $this->generateUrl('backend_room_promotion_benefit_hotel_service_discount_create', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid]),
                'attr' => array(
                    'id'    => 'roomPromotionForm'
                    ),
                'method' => 'POST',
            ));            
        }
        

        // $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }
    
    /**
    * Creates a form to edit a Promotion entity.
    *
    * @param Promotion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PromotionBenefit $entity, $hid, $rid, $pid, $bid, $type)
    {

        if ($type === 'NIGHT-DISCOUNT') {
            $form = $this->createForm(new PromotionBenefitNightDiscountType(), $entity, array(
                'action' => $this->generateUrl('backend_room_promotion_benefit_night_discount_update', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'bid' => $bid]),
                'attr' => array(
                    'id'    => 'roomPromotionForm'
                    ),
                'method' => 'PUT',
            ));

        } elseif ($type === 'STAY-DISCOUNT') {
            $form = $this->createForm(new PromotionBenefitStayDiscountType(), $entity, array(
                'action' => $this->generateUrl('backend_room_promotion_benefit_stay_discount_update', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'bid' => $bid]),
                'attr' => array(
                    'id'    => 'roomPromotionForm'
                    ),
                'method' => 'PUT',
            ));

        } elseif ($type === 'ROOM-SERVICE-DISCOUNT') {

            $form = $this->createForm(new PromotionBenefitRoomServiceDiscountType(), $entity, array(
                'action' => $this->generateUrl('backend_room_promotion_benefit_room_service_discount_update', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'bid' => $bid]),
                'attr' => array(
                    'id'    => 'roomPromotionForm'
                    ),
                'method' => 'PUT',
            ));

        } elseif ($type === 'HOTEL-SERVICE-DISCOUNT') {

            $form = $this->createForm(new PromotionBenefitHotelServiceDiscountType(), $entity, array(
                'action' => $this->generateUrl('backend_room_promotion_benefit_hotel_service_discount_update', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'bid' => $bid]),
                'attr' => array(
                    'id'    => 'roomPromotionForm'
                    ),
                'method' => 'PUT',
            ));

        }



        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }    

    /**
     * Creates a form to delete a Promotion entity by id.
     *
     * @param mixed $pid The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($hid, $rid, $pid, $bid)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'roomPromotionDeleteForm')))
            ->setAction($this->generateUrl('backend_room_promotion_benefit_delete', ['hid' => $hid, 'rid' => $rid, 'pid' => $pid, 'bid' => $bid]))
            ->setMethod('DELETE')
            // ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
