<?php
namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedResponse;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Entity\Management\User;

/**
 * Description of CommentController
 *
 * @author marcducobu [arobase] gmail POINT com 
 * @author julien [arobase] fastre POINT info
 */
class CommentController extends Controller 
{
    private function getCommentByPLaceLimit($_format, $placeId, $limit, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $place = $em->getRepository("ProgracqteurWikipedaleBundle:Model\\Place")->find($placeId);
        
        if ($place === null)
        {
            throw $this->createNotFoundException("La place $placeId n'a pas été trouvée");
        }
        
        $q = $em->createQuery("SELECT cm from ProgracqteurWikipedaleBundle:Model\\Comment cm 
            where cm.place = :place and cm.published = true ORDER BY cm.id DESC")
                ->setParameter('place',$place);

        if($limit != null)
        {
            $q->setMaxResults($limit);
        }
        
        
        $comments = $q->getResult();
        
        switch($_format)
        {
            case 'json':
                $response = new NormalizedResponse();
                $response->setResults($comments);
                
                $serializer = $this->get('progracqteurWikipedaleSerializer');
                $string = $serializer->serialize($response, $_format);
                
                return new Response($string);
                break;
            default:
                throw new \Exception("le format $_format est inconnu");
        }
    }

    public function getLastCommentByPlaceAction($_format, $placeId, Request $request)
    {
        return $this->getCommentByPLaceLimit($_format, $placeId, 1, $request);
    }

    public function getCommentByPlaceAction($_format, $placeId,Request $request)
    {
        return $this->getCommentByPLaceLimit($_format, $placeId, null, $request);
    }
    
    public function newbisAction($placeId, $_format, Request $request)
    {
        if ($request->getMethod() != 'POST')
        {
            throw new \Exception("Only post method accepted");
        }

        if (!$this->get('security.context')->getToken()->getUser() instanceof User)
        {
            throw new AccessDeniedException('Vous devez être un enregistré pour ajouter un commentaire');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $place = $em->getRepository("ProgracqteurWikipedaleBundle:Model\\Place")
                ->find($placeId);
        
        if ($place === null)
        {
            throw $this->createNotFoundException("La place $placeId n'a pas été trouvée");
        }

        $serializedJson = $request->get('entity', null);
        
        if ($serializedJson === null)
        {
            throw new \Exception("Aucune entitée envoyée");
        }

        $serializer = $this->get('progracqteurWikipedaleSerializer');
        
        $comment = $serializer->deserialize($serializedJson, 
                NormalizerSerializerService::COMMENT_TYPE, 
                $_format);

        $user = $this->get('security.context')->getToken()->getUser();
        if ($user instanceof User) { //si user is logger
            $comment->setCreator($user);
        }
        else {
            throw new \Exception("Il faut être connecté pour ajouter un commentaire");
        }

        if (! $this->get('security.context')->isGranted(User::ROLE_NOTATION)) {
            throw new \Exception("Vous n'avez pas le droit d'ajouter un commentaire");
        }

        //print "plus de verif pour les droits";

        $em->persist($comment);
        $em->flush();
        
        return $this->redirect(
                $this->generateUrl('wikipedale_comment_view', array( 
                    'commentId' => $comment->getId(),
                    '_format' => $_format
                ))
             );
    }
    
    public function changeAction($_format, Request $request)
    {
        if ($request->getMethod() != 'POST')
        {
            throw new \Exception("Only post method accepted");
        }

        if (!$this->get('security.context')->getToken()->getUser() instanceof User)
        {
            throw new AccessDeniedException('Vous devez être un enregistré pour ajouter un commentaire');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $serializedJson = $request->get('entity', null);
        
        if ($serializedJson === null)
        {
            throw new \Exception("Aucune entitée envoyée");
        }

        $serializer = $this->get('progracqteurWikipedaleSerializer');
        
        $comment = $serializer->deserialize($serializedJson, 
                NormalizerSerializerService::COMMENT_TYPE, 
                $_format);

        $user = $this->get('security.context')->getToken()->getUser();
        if ($user instanceof User) { //si user is logger
            $comment->setCreator($user);
        }
        else {
            throw new \Exception("Il faut être connecté pour ajouter un commentaire");
        }

        if (! $this->get('security.context')->isGranted(User::ROLE_NOTATION)) {
            throw new \Exception("Vous n'avez pas le droit d'ajouter un commentaire");
        }
        
        

        //print "plus de verif pour les droits";

        $em->persist($comment);
        
        //add user to comment
        $comment->getPlace()->getChangeset()->setAuthor($this->get('security.context')->getToken()->getUser());
        
        $em->flush();
        

        
        return $this->redirect(
                $this->generateUrl('wikipedale_comment_view', array( 
                    'commentId' => $comment->getId(),
                    '_format' => $_format
                ))
             );
    }
    
    public function viewAction($commentId, $_format)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $comment = $em->getRepository('ProgracqteurWikipedaleBundle:Model\\Comment')
                ->find($commentId);
        
        if ($comment === null)
        {
            throw $this->createNotFoundException("comment with id $commentId not found");
        }
        
        $serializer = $this->get('progracqteurWikipedaleSerializer');
        
        $rep = new NormalizedResponse(array($comment));
        
        $text = $serializer->serialize($rep, $_format);
        
        switch($_format) 
        {
            case 'json' : 
                return new Response($text);
                break;
            default:
                $r = new Response('format demandé indisponible');
                $r->setStatusCode(400);
                return $r;
        }
        
    }
    
    
    
}

