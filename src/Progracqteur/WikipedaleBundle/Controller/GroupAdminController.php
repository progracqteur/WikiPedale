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
        
        $d = $this->getDoctrine()->getManager(); 
        
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
        $form = $this->createForm(new GroupType($this->getDoctrine()->getManager()), $g);
        
        if ($request->getMethod() === 'POST')
        {
            $form->bind($request);
            
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($g);
                $em->flush();
                
                
                $this->get('session')->getFlashBag()->add('notice', $this->get('translator')
                           ->trans('groups.created'));
                return $this->redirect(
                            $this->generateUrl('wikipedale_groups_list')
                        );
                
            } else {
                $this->get('session')->getFlashBag()->add('notice', $this->get('translator')
                           ->trans("echec"));
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
        
        $em = $this->getDoctrine()->getManager();
        
        $g = $em->getRepository('ProgracqteurWikipedaleBundle:Management\Group')
                ->find($id);
        
        
        if ($g === null)
        {
            return $this->createNotFoundException("group.non.found");
        }
        
        $form = $this->createForm(new GroupType($this->getDoctrine()->getManager()), $g);
        
        if ($request->getMethod() === 'POST')
        {
            $form->bind($request);
            
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($g);
                $em->flush();
                
                
                $this->get('session')->getFlashBag()->add('notice', $this->get('translator')
                           ->trans('groups.updated'));
                return $this->redirect(
                            $this->generateUrl('wikipedale_groups_list')
                        );
                
            } else {
                $this->get('session')->getFlashBag()->add('notice', $this->get('translator')
                           ->trans("echec"));
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
        $max = (int) $request->get('max', 50);
        
        $em = $this->getDoctrine()->getManager();
        
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
        
        $em = $this->getDoctrine()->getManager();
        
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
        
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('ProgracqteurWikipedaleBundle:Management\User')
                ->find($id);
        
        if (null === $user)
        {
            throw $this->createNotFoundException('user.not.found');
        }
        
        $formGroups = $this->createForm(new GroupUserType(), $user);
        
        if ($request->getMethod() == "POST")
        {
            $formGroups->bind($request);
            
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
                        if ($group->getZone()->getId() === $notification->getZone()->getId()) {
                            $notificationMatchGroup = true;
                            break;
                        }
                        
                        /*if ($notification->getGroup() !== null)
                        {
                            if ($notification->getGroup()->getId() === $group->getId())
                            {
                                $notificationMatchGroup = true;
                                break;
                            }
                            
                        } */
                    }

                    
                    if ($notificationMatchGroup === false)
                    {
                        $user->removeNotificationSubscription($notification);
                        $em->remove($notification);
                    }
                    
                    
                }

                
                $em->flush();
                $this->get('session')->getFlashBag()->add('notice', 
                        $this->get('translator')
                           ->trans('admin.user.groups_added_or_removed'));
                
                return $this->redirect(
                        $this->generateUrl('wikipedale_admin_usergroups_update',
                                array('id' => $user->getId())
                            )
                        );
            }
            
        }
        //if not valid : (not POST or not valid form)
        $this->get('session')->getFlashBag()->add('notice',
                    $this->get('translator')
                           ->trans('user.groups.error_adding_or_removing_group'));
        return $this->redirect(
                        $this->generateUrl('wikipedale_admin_usergroups_update',
                                array('id' => $user->getId())
                            )
                        );
    }
    
    
    public function userShowFormAction($id, Request $request) {
        if (! $this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            return new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        
        
        $user = $this->getDoctrine()->getManager()
                ->getRepository('ProgracqteurWikipedaleBundle:Management\User')
                ->find($id);
        
        if ($user === null) {
            throw $this->createNotFoundException('user '.$id.' not found');
        }
        
        $form = $this->createForm('wikipedale_user_admin_profile', $user);
        
        if ($request->getMethod() === "POST") {
            $form->bind($request);
            
            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                
                $this->get('session')->getFlashBag()->add('notice',
                    $this->get('translator')
                           ->trans('admin.profile_user.user_updated'));
                
                return $this->redirect(
                        $this->generateUrl('wikipedale_admin_usergroups')
                        );
            } else {
                $this->get('session')->getFlashBag()->add('notice',
                    $this->get('translator')
                           ->trans('admin.profile_user.contain_errors'));
            }
        }
        
        return $this->render('ProgracqteurWikipedaleBundle:Management/User:form.html.twig', 
                array(
                    'form' => $form->createView(), 
                    'user' => $user,
                    'action' => 
                        $this->generateUrl('wikipedale_admin_user_show_form', 
                                array('id' => $user->getId()))
                )
                );
        
    }
    
    
    public function newVirtualUserAction() {
        if (! $this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            return new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        
        $user = new \Progracqteur\WikipedaleBundle\Entity\Management\User();
        $user->setVirtual(true);
        $form = $this->createForm('wikipedale_user_admin_profile', $user);

        
        return $this->render('ProgracqteurWikipedaleBundle:Management/User:form.html.twig', 
                array(
                    'form' => $form->createView(), 
                    'user' => $user, 
                    'action' => 
                        $this->generateUrl('wikipedale_admin_user_create_virtual') 
                )
                );
    }
    
    public function createVirtualUserAction(Request $request) {
        if (! $this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            return new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        
        if ($request->getMethod() === 'POST') {
            $user = new \Progracqteur\WikipedaleBundle\Entity\Management\User();
        
            $form = $this->createForm('wikipedale_user_admin_profile', $user);
            
            $form->bind($request);
            
            $user->setUsername($user->getLabel());
            
            $password = sha1(uniqid( rand() ));
            $user->setPassword($password);
            
            if (! $user->isVirtual()) {
                $user->setVirtual(true);
                
                $this->get('session')->getFlashBag()->add('notice', 
                        $this->get('translator')
                           ->trans('admin.user.transform_to_virtual'));
            }
            
            $em = $this->getDoctrine()->getManager();
                
            $em->persist($user);

            $em->flush();
            
            
            $this->get('session')->getFlashBag()->add('notice', 
                        $this->get('translator')
                           ->trans('admin.user.added', 
                                   array('%label%' => $user->getLabel()
                    )));
            
            return $this->redirect($this->generateUrl('wikipedale_admin_user_show_form',
                    array('id' => $user->getId() 
                    )));
            
        } else {
            return $this->redirect(
                    $this->generateUrl('wikipedale_admin_user_create_virtual'));
        }
        
        
        
        
        
        
    }
    

    
}

