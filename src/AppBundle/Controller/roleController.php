<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\role;
use AppBundle\Form\roleType;

/**
 * role controller.
 *
 */
class roleController extends Controller
{
    /**
     * Lists all role entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:role')->findAll();

        return $this->render('AppBundle:role:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a role entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find role entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:role:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new role entity.
     *
     */
    public function newAction()
    {
        $entity = new role();
        $form   = $this->createForm(new roleType(), $entity);

        return $this->render('AppBundle:role:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new role entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new role();
        $form = $this->createForm(new roleType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('role_show', array('id' => $entity->getId())));
        }

        return $this->render('AppBundle:role:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing role entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find role entity.');
        }

        $editForm = $this->createForm(new roleType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:role:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing role entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find role entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new roleType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('role_edit', array('id' => $id)));
        }

        return $this->render('AppBundle:role:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a role entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:role')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find role entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('role'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
