<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
/**
 * Description of NotificationController
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationsController extends Controller {
    
    public function indexAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        
        //if user is not connected
        if (!($user instanceof User)) {
            throw new AccessDeniedException('you must be registered for accessing this page');
        }
        
        $notifications = $user->getNotificationSubscriptions();
        
        return $this->render('ProgracqteurWikipedaleBundle:NotificationSubscriptions:list.html.twig', 
                array(
                    'notifications' => $notifications,
                    'user' => $user)
                );
    }
    
    
}

