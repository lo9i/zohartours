<?php

namespace Gushh\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\DBAL\DBALException;
use Gushh\CoreBundle\Entity\User;
use Gushh\CoreBundle\Form\AdminType;
use Gushh\CoreBundle\Form\EditAdminType;
use Gushh\CoreBundle\Form\OperatorType;
use Gushh\CoreBundle\Form\EditOperatorType;
use Gushh\CoreBundle\Form\ViewerType;
use Gushh\CoreBundle\Form\EditViewerType;

/**
 * User controller.
 *
 * @Route("/dashboard/user")
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @Route("s/", name="backend_users")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $entities = $this->getDoctrine()
                          ->getManager()
                          ->getRepository('GushhCoreBundle:User')
                          ->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all Agency User entities.
     *
     * @Route("/operators/", name="backend_user_operators")
     * @Method("GET")
     * @Template()
     */
    public function operatorAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GushhCoreBundle:User')->findByRole('ROLE_AGENCY');

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all Agency User entities.
     *
     * @Route("/viewers/", name="backend_user_viewers")
     * @Method("GET")
     * @Template()
     */
    public function viewerAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GushhCoreBundle:User')->findByRole('ROLE_VIEWER');

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all Admin User entities.
     *
     * @Route("/admins/", name="backend_user_admins")
     * @Method("GET")
     * @Template()
     */
    public function adminAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GushhCoreBundle:User')->findByRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/operator/new/", name="backend_user_operator_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:User:newOperator.html.twig")
     */
    public function createOperatorAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity, 'OPERATOR');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setUsername($entity->getEmail());
            $entity->addRole('ROLE_AGENCY');
            $entity->setEnabled(true);

            try{
                $em->persist($entity);
                $em->flush();
            }
            catch( DBALException $ex )
            {
                if ($ex->getPrevious() &&  0 === strpos($ex->getPrevious()->getCode(), '23')) {

                    $this->getDoctrine()->resetManager();
                    $em = $this->getDoctrine()->getManager();
                    $existingUser = $em->getRepository('GushhCoreBundle:User')->findOneBy(array( 'usernameCanonical' => $entity->getUsernameCanonical() ));

                    if (!$existingUser) {
                        throw $this->createNotFoundException('Unable to find User entity.');
                    }

                    $existingUser->removeRole('ROLE_ADMIN');
                    $existingUser->removeRole('ROLE_SUPER_ADMIN');
                    $existingUser->addRole('ROLE_AGENCY');
                    $existingUser->setEnabled(true);
                    $existingUser->setPassword($entity->getPassword());
                    $existingUser->setSalt($entity->getSalt());
                    $em->persist($existingUser);
                    $em->flush();
                }
                else {
                    throw $ex;
                }
            }

            return $this->redirect($this->generateUrl('backend_user_operators'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Viewer entity.
     *
     * @Route("/viewer/new/", name="backend_user_viewer_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:User:newViewer.html.twig")
     */
    public function createViewerAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity, 'VIEWER');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setUsername($entity->getEmail());
            $entity->addRole('ROLE_VIEWER');
            $entity->setEnabled(true);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_user_viewers'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    /**
     * Creates a new User entity.
     *
     * @Route("/admin/new/", name="backend_user_admin_create")
     * @Method("POST")
     * @Template("GushhBackendBundle:User:newAdmin.html.twig")
     */
    public function createAdminAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity, 'ADMIN');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setUsername($entity->getEmail());
            if ($entity->getSuperadmin()) {
                $entity->removeRole('ROLE_ADMIN');
                $entity->addRole('ROLE_SUPER_ADMIN');
            } else {
                $entity->removeRole('ROLE_SUPER_ADMIN');
                $entity->addRole('ROLE_ADMIN');

            }
            $entity->setEnabled(true);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_user_admins'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity, $type = 'OPERATOR')
    {

        if ($type === 'OPERATOR') {
            $form = $this->createForm(new OperatorType(), $entity, array(
                'action' => $this->generateUrl('backend_user_operator_create'),
                'attr' => array(
                    'id'    => 'newUserForm',
                    'autocomplete' => 'off'
                    ),
                'method' => 'POST',
            ));
        } elseif ($type === 'VIEWER') {
            $form = $this->createForm(new ViewerType(), $entity, array(
                'action' => $this->generateUrl('backend_user_viewer_create'),
                'attr' => array(
                    'id'    => 'newUserForm',
                    'autocomplete' => 'off'
                ),
                'method' => 'POST',
            ));
        } elseif ($type === 'ADMIN') {
            $form = $this->createForm(new AdminType(), $entity, array(
                'action' => $this->generateUrl('backend_user_admin_create'),
                'attr' => array(
                    'id'    => 'newUserForm',
                    'autocomplete' => 'off'
                ),
                'method' => 'POST',
            ));
        }

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/operator/new/", name="backend_user_operator_new")
     * @Method("GET")
     * @Template()
     */
    public function newOperatorAction()
    {

        $entity = new User();


        $form   = $this->createCreateForm($entity, 'OPERATOR');

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/viewer/new/", name="backend_user_viewer_new")
     * @Method("GET")
     * @Template()
     */
    public function newViewerAction()
    {

        $entity = new User();


        $form   = $this->createCreateForm($entity, 'VIEWER');

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/admin/new/", name="backend_user_admin_new")
     * @Method("GET")
     * @Template()
     */
    public function newAdminAction()
    {

        $entity = new User();

        $form   = $this->createCreateForm($entity, 'ADMIN');

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}/", name="backend_user_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/operator/{id}/edit/", name="backend_user_operator_edit")
     * @Method("GET")
     * @Template()
     */
    public function editOperatorAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $roles = $entity->getRoles();

        if ( !in_array('ROLE_AGENCY', $roles) ) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity, 'OPERATOR');
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'        => $entity,
            'form'          => $editForm->createView(),
            'deleteForm'    => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/viewer/{id}/edit/", name="backend_user_viewer_edit")
     * @Method("GET")
     * @Template()
     */
    public function editViewerAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $roles = $entity->getRoles();

        if ( !in_array('ROLE_VIEWER', $roles) ) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity, 'VIEWER');
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'        => $entity,
            'form'          => $editForm->createView(),
            'deleteForm'    => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/admin/{id}/edit/", name="backend_user_admin_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAdminAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $roles = $entity->getRoles();

        if ( !in_array('ROLE_ADMIN', $roles) AND !in_array('ROLE_SUPER_ADMIN', $roles)) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity, 'ADMIN');
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'        => $entity,
            'form'          => $editForm->createView(),
            'deleteForm'    => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity, $type = 'OPERATOR')
    {
        if ($type === 'OPERATOR') {
            $form = $this->createForm(new EditOperatorType(), $entity, array(
                'action' => $this->generateUrl('backend_user_operator_update', array('id' => $entity->getId())),
                'attr' => array(
                    'id'    => 'editUserForm',
                    'autocomplete' => 'off'
                ),
                'method' => 'PUT',
            ));
        } elseif ($type === 'VIEWER') {
            $form = $this->createForm(new EditViewerType(), $entity, array(
                'action' => $this->generateUrl('backend_user_viewer_update', array('id' => $entity->getId())),
                'attr' => array(
                    'id'    => 'editUserForm',
                    'autocomplete' => 'off'
                ),
                'method' => 'PUT',
            ));
        } elseif ($type === 'ADMIN') {
            $form = $this->createForm(new EditAdminType(), $entity, array(
                'action' => $this->generateUrl('backend_user_admin_update', array('id' => $entity->getId())),
                'attr' => array(
                    'id'    => 'editUserForm',
                    'autocomplete' => 'off'
                ),
                'method' => 'PUT',
            ));
        }

        // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/operator/{id}/", name="backend_user_operator_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:User:editOperator.html.twig")
     */
    public function updateOperatorAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity, 'OPERATOR');
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if ($entity->getEmail() !== $entity->getUsername()) {
                $entity->setUsername($entity->getEmail());
                $em->persist($entity);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('backend_user_operators'));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/viewer/{id}/", name="backend_user_viewer_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:User:editViewer.html.twig")
     */
    public function updateViewerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity, 'VIEWER');
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if ($entity->getEmail() !== $entity->getUsername()) {
                $entity->setUsername($entity->getEmail());
                $em->persist($entity);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('backend_user_viewers'));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/admin/{id}/", name="backend_user_admin_update")
     * @Method("PUT")
     * @Template("GushhBackendBundle:User:editAdmin.html.twig")
     */
    public function updateAdminAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GushhCoreBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity, 'ADMIN');
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if (!in_array($id, [1, 2])) {
                if ($entity->getSuperadmin()) {
                    $entity->removeRole('ROLE_ADMIN');
                    $entity->addRole('ROLE_SUPER_ADMIN');
                } else {
                    $entity->removeRole('ROLE_SUPER_ADMIN');
                    $entity->addRole('ROLE_ADMIN');

                }

                if ($entity->getEmail() !== $entity->getUsername()) {
                    $entity->setUsername($entity->getEmail());
                    $em->persist($entity);
                }

            } else {
                if ($id == 1) {
                    $entity->setName('Anibal');
                    $entity->setLastname('Fernández');
                    $entity->setEmail('anibaldfernandez@hotmail.com');
                    $entity->setUsername('anibaldfernandez@hotmail.com');
                } elseif ($id == 2) {
                    $entity->setEmail('guillermo@zohartours.com');
                    $entity->setUsername('guillermo@zohartours.com');
                }

                $entity->setSuperadmin(true);
                $em->persist($entity);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('backend_user_admins'));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'deleteForm' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}/", name="backend_user_delete")
     * @Method("PUT")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GushhCoreBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            if ($id == 2 ) {
                throw $this->createNotFoundException('Impossible delete this user.');
            }

             $entity->setEnabled(false);
            //$em->remove($entity);
             $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_users'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(null, ['attr' => ['id' => 'userDeleteForm']])
            ->setAction($this->generateUrl('backend_user_delete', ['id' => $id]))
            ->setMethod('PUT')
            ->getForm()
            // ->add('submit', 'submit', array('label' => 'Delete'))
        ;
    }
}
