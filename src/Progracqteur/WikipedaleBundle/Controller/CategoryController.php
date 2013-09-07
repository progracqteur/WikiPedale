<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Progracqteur\WikipedaleBundle\Entity\Model\Category;
use Progracqteur\WikipedaleBundle\Form\Model\CategoryType;

/**
 * Model\Category controller.
 *
 */
class CategoryController extends Controller
{
    /**
     * Lists all Model\Category entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('ProgracqteurWikipedaleBundle:Model\Category')->findAll();

        return $this->render('ProgracqteurWikipedaleBundle:Model/Category:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Model\Category entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProgracqteurWikipedaleBundle:Model\Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Model\Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProgracqteurWikipedaleBundle:Model/Category:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Model\Category entity.
     *
     */
    public function newAction()
    {
        $entity = new Category();
        $form   = $this->createForm(
                new CategoryType($this->getDoctrine()->getEntityManager()), $entity);

        return $this->render('ProgracqteurWikipedaleBundle:Model/Category:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Model\Category entity.
     *
     */
    public function createAction()
    {
        $entity  = new Category();
        $request = $this->getRequest();
        $form    = $this->createForm(
                new CategoryType($this->getDoctrine()->getEntityManager()), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_category_show', array('id' => $entity->getId())));
            
        }

        return $this->render('ProgracqteurWikipedaleBundle:Model/Category:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Model\Category entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProgracqteurWikipedaleBundle:Model\Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Model\Category entity.');
        }

        $editForm = $this->createForm(new CategoryType(
                $this->getDoctrine()->getEntityManager()), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProgracqteurWikipedaleBundle:Model/Category:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Model\Category entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProgracqteurWikipedaleBundle:Model\Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Model\Category entity.');
        }

        $editForm   = $this->createForm(new CategoryType(
                $this->getDoctrine()->getEntityManager()), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_category_edit', array('id' => $id)));
        }

        return $this->render('ProgracqteurWikipedaleBundle:Model/Category:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Model\Category entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('ProgracqteurWikipedaleBundle:Model\Category')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Model\Category entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_category'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
