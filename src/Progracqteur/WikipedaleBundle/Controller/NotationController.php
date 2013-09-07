<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Progracqteur\WikipedaleBundle\Entity\Management\Notation;
use Progracqteur\WikipedaleBundle\Form\Management\NotationType;

/**
 * Notation controller.
 *
 */
class NotationController extends Controller
{
    /**
     * Lists all Notation entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('ProgracqteurWikipedaleBundle:Management\Notation')->findAll();

        return $this->render('ProgracqteurWikipedaleBundle:Management/Notation:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Notation entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProgracqteurWikipedaleBundle:Management\Notation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProgracqteurWikipedaleBundle:Management/Notation:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Notation entity.
     *
     */
    public function newAction()
    {
        $entity = new Notation();
        $form   = $this->createForm(new NotationType(), $entity);

        return $this->render('ProgracqteurWikipedaleBundle:Management/Notation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Notation entity.
     *
     */
    public function createAction()
    {
        $entity  = new Notation();
        $request = $this->getRequest();
        $form    = $this->createForm(new NotationType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('wikipedale_notations_show', array('id' => $entity->getId())));
            
        }

        return $this->render('ProgracqteurWikipedaleBundle:Management/Notation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Notation entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProgracqteurWikipedaleBundle:Management\Notation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notation entity.');
        }

        $editForm = $this->createForm(new NotationType('update'), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProgracqteurWikipedaleBundle:Management/Notation:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Notation entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProgracqteurWikipedaleBundle:Management\Notation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notation entity.');
        }

        $editForm   = $this->createForm(new NotationType('update'), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('wikipedale_notations_edit', array('id' => $entity->getId())));
        }

        return $this->render('ProgracqteurWikipedaleBundle:Management/Notation:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Notation entity.
     *
     */
    public function deleteAction($id)
    {
        throw new \Exception('Cette action est interdite');
        
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('ProgracqteurWikipedaleBundle:Management\Notation')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Notation entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
