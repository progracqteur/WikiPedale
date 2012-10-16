<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;
use Progracqteur\WikipedaleBundle\Form\Management\GroupType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of GroupAdminController
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class GroupAdminController extends Controller {
    
    public function listAction()
    {
        if (! $this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            return new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        
        $d = $this->getDoctrine()->getEntityManager(); 
        
        $groups = $d->getRepository('ProgracqteurWikipedaleBundle:Management\Group')
                ->findAll();
        
        return $this->render('ProgracqteurWikipedaleBundle:Groups:list.html.twig', array(
            'groups' => $groups
        ));
        
    }
    
    public function createAction(Request $request)
    {
        if (! $this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            return new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        
        $g = new Group('');
        $form = $this->createForm(new GroupType($this->getDoctrine()->getEntityManager()), $g);
        
        if ($request->getMethod() === 'POST')
        {
            $form->bindRequest($request);
            
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($g);
                $em->flush();
                
                
                $this->get('session')->setFlash('notice', 'groups.created');
                return $this->redirect(
                            $this->generateUrl('wikipedale_groups_list')
                        );
                
            } else {
                $this->get('session')->setFlash('notice', "echec");
            }
        }
        
        
        return $this->render('ProgracqteurWikipedaleBundle:Groups:form.html.twig', array(
            'form' => $form->createView(),
            'title' => 'create'
        ));
    }
    
    public function updateAction($id, Request $request)
    {
        if (! $this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            return new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $g = $em->getRepository('ProgracqteurWikipedaleBundle:Management\Group')
                ->find($id);
        
        
        if ($g === null)
        {
            return $this->createNotFoundException("group.non.found");
        }
        
        $form = $this->createForm(new GroupType($this->getDoctrine()->getEntityManager()), $g);
        
        if ($request->getMethod() === 'POST')
        {
            $form->bindRequest($request);
            
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($g);
                $em->flush();
                
                
                $this->get('session')->setFlash('notice', 'groups.updated');
                return $this->redirect(
                            $this->generateUrl('wikipedale_groups_list')
                        );
                
            } else {
                $this->get('session')->setFlash('notice', "echec");
            }
        }
        
        return $this->render('ProgracqteurWikipedaleBundle:Groups:form.html.twig', array(
            'form' => $form->createView(),
            'title' => 'update'
        ));
    }
    
}

