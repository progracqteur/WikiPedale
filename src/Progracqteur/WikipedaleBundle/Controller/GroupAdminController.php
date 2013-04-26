<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;
use Progracqteur\WikipedaleBundle\Form\Management\GroupType;
use Symfony\Component\HttpFoundation\Request;
use Progracqteur\WikipedaleBundle\Form\Management\GroupUser\GroupUserType;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;

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
    
    
    public function userListAction(Request $request)
    {
        if (! $this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            return new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        
        $query = $request->get('q', '');
        $first = (int) $request->get('first', 0);
        $max = (int) $request->get('max', 20);
        
        $em = $this->getDoctrine()->getEntityManager();
        
        if ($query !== '')
        {
            $users = $em->createQuery('Select u from ProgracqteurWikipedaleBundle:Management\User u
                LEFT JOIN u.groups g
                where u.username LIKE :query 
                ORDER BY u.username')
                    ->setParameter('query', '%'.$query.'%')
                    ->setFirstResult($first)
                    ->setMaxResults($max)
                    ->getResult()
                    ;
            
            $nb = $em->createQuery('select count (u.id) from ProgracqteurWikipedaleBundle:Management\User u
                where u.username LIKE :query')
                    ->setParameter('query', '%'.$query.'%')
                    ->getSingleResult()
                    ;
            
        } else {
            $users = $em->createQuery('Select u from 
                ProgracqteurWikipedaleBundle:Management\User u
                LEFT JOIN u.groups g
                ORDER BY u.username')
                    ->setFirstResult($first)
                    ->setMaxResults($max)        
                    ->getResult()
                    ;
            $nb = $em->createQuery('select count (u.id) from ProgracqteurWikipedaleBundle:Management\User u')
                    ->getSingleResult()
                    ;
        }
        $nb = $nb[1];
        $a = $nb/$max;
        $nbPages = round($a, 0);
        $a = $first/$max;
        $thisPage = round($a,0);
        
        return $this->render('ProgracqteurWikipedaleBundle:Management\UserGroup:list.html.twig',
                array(
                    'users' => $users,
                    'nb' => $nb,
                    'nb_pages' => $nbPages,
                    'this_page' => $thisPage,
                    'query' => $query,
                    'first' => $first,
                    'max' => $max
                    
                    )
                
                );
        
                
        
    }
    
    
    public function userUpdateAction($id, Request $request)
    {
        if (! $this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            return new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $user = $em->getRepository('ProgracqteurWikipedaleBundle:Management\User')
                ->find($id);
        
        if (null === $user)
        {
            throw $this->createNotFoundException('user.not.found');
        }
        
        $formGroups = $this->createForm(new GroupUserType(), $user);
               
        return $this->render('ProgracqteurWikipedaleBundle:Management\UserGroup:view.html.twig', array(
                'user' => $user,
                'formGroup' => $formGroups->createView(),
            ));
        
    }
    
    public function addRemoveGroupsAction($id, Request $request)
    {
        if (! $this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            return new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $user = $em->getRepository('ProgracqteurWikipedaleBundle:Management\User')
                ->find($id);
        
        if (null === $user)
        {
            throw $this->createNotFoundException('user.not.found');
        }
        
        $formGroups = $this->createForm(new GroupUserType(), $user);
        
        if ($request->getMethod() == "POST")
        {
            $formGroups->bindRequest($request);
            
            if ($formGroups->isValid())
            {
                //add notificationSubscriptions
                $user = $formGroups->getData();
                
                //add notification to group recently added
                foreach($user->getGroups() as $group)
                {
                    $groupAlreadyNotified = false;
                    
                    foreach($user->getNotificationSubscriptions() as $notification)
                    {
                        if ($notification->getGroup() !== null 
                                && $notification->getGroup()->getId() === $group->getId())
                        {
                            $groupAlreadyNotified = true;
                        } 
                    }
                    
                    if ($groupAlreadyNotified === false)
                    {
                        switch ($group->getType())
                        {
                            case Group::TYPE_MANAGER:
                                $notification = new NotificationSubscription();
                                $notification->setKind(NotificationSubscription::KIND_MANAGER);
                                break;
                            case Group::TYPE_MODERATOR:
                                $notification = new NotificationSubscription();
                                $notification->setKind(NotificationSubscription::KIND_MODERATOR);
                                break;
                            default:
                                $notification = null;
                                break;

                        }

                        if ($notification !== null)
                        {
                            $notification->setFrequency(NotificationSubscription::FREQUENCY_MINUTELY)
                                        ->setGroup($group)
                                        ->setOwner($user)
                                        ->setZone($group->getZone())
                                    ;
                            $user->addNotificationSubscription($notification);

                        }
                    } 
                }
                
                //remove notification to groups recently removed
                foreach($user->getNotificationSubscriptions() as $notification)
                {
                    
                    $notificationMatchGroup = false;
                    
                    foreach ($user->getGroups() as $group)
                    {
                        if ($notification->getGroup() !== null)
                        {
                            if ($notification->getGroup()->getId() === $group->getId())
                            {
                                $notificationMatchGroup = true;
                                break;
                            }
                            
                        } 
                    }

                    
                    if ($notificationMatchGroup === false)
                    {
                        $user->removeNotificationSubscription($notification);
                        $em->remove($notification);
                    }
                    
                    
                }

                
                $em->flush();
                $this->get('session')->setFlash('notice', 
                        'user.groups.added_or_removed');
                
                return $this->redirect(
                        $this->generateUrl('wikipedale_admin_usergroups_update',
                                array('id' => $user->getId())
                            )
                        );
            }
            
        }
        //if not valid : (not POST or not valid form)
        $this->get('session')->setFlash('notice',
                    'user.groups.error_adding_or_removing_group');
        return $this->redirect(
                        $this->generateUrl('wikipedale_admin_usergroups_update',
                                array('id' => $user->getId())
                            )
                        );
    }
    
}

