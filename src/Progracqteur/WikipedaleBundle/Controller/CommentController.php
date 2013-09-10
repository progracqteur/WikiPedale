<?php
namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedResponse;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Entity\Model\Comment;

/**
 * Description of CommentController
 *
 * @author marcducobu [arobase] gmail POINT com 
 * @author julien [arobase] fastre POINT info
 */
class CommentController extends Controller 
{
    
    const MAX_COMMENTS_BY_REQUEST = 40;
    
    private function getCommentByPLaceLimit($_format, $placeId, $limit, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $place = $em->getRepository("ProgracqteurWikipedaleBundle:Model\\Place")->find($placeId);
        
        if ($place === null OR $place->isAccepted() === false)
        {
                                                    //TODO: i18n
            throw $this->createNotFoundException("La place $placeId n'a pas été trouvée");
        }
        
        $qstring = "SELECT cm 
            FROM ProgracqteurWikipedaleBundle:Model\\Comment cm 
            WHERE 
            cm.place = :place 
            and cm.published = true 
            ";
        
        $q = $em->createQuery()
                ->setParameter('place',$place);
        
        $countQuery = $em->createQuery()
                ->setParameter('place', $place);
        
        
        //create a where clause depending on the user's roles
        $strCommentTypeCondition = '';
        
        //add default type
        $strCommentTypeCondition .= "cm.type = :public";
        $q->setParameter('public', Comment::TYPE_PUBLIC);
        $countQuery->setParameter('public', Comment::TYPE_PUBLIC);
        
        //add depending on roles
        if ($this->get('security.context')->isGranted(User::ROLE_COMMENT_MODERATOR_MANAGER)) {

            if ($strCommentTypeCondition !== '') {
                $strCommentTypeCondition .= ' OR ';
            }

            $strCommentTypeCondition .= 'cm.type = :moderator_manager';
            $q->setParameter('moderator_manager', Comment::TYPE_MODERATOR_MANAGER);
            $countQuery->setParameter('moderator_manager', Comment::TYPE_MODERATOR_MANAGER);
        }

        
        $qstring .= " AND (".$strCommentTypeCondition.") ";
        
        
        $qstring .= " ORDER BY cm.creationDate DESC ";
        
        $q->setDql($qstring);
        
        $limit = $request->query->get('max', null);

        if($limit !== null)
        {
            if ($limit > self::MAX_COMMENTS_BY_REQUEST) {
                $limit = self::MAX_COMMENTS_BY_REQUEST;
            }
            $q->setMaxResults($limit);
            
        } else {
            
            $q->setMaxResults(self::MAX_COMMENTS_BY_REQUEST);
            
        }
        
        $first = $request->query->get('first', null);
        
        if ($first < 0) {
            $response = new Response('le paramètre first ne peut pas être négatif');
            $response->setStatusCode(400);
            return $response;
        }
        
        if ($first !== null){
            $q->setFirstResult($first);
        }
        
        
        $comments = $q->getResult();
        
        $countQueryDQLString = 'SELECT count(cm.id) 
            FROM ProgracqteurWikipedaleBundle:Model\Comment cm
            WHERE
            cm.place = :place 
            and cm.published = true AND ('.$strCommentTypeCondition.') ';
                
        
        $count = $countQuery->setDql($countQueryDQLString)
                ->getSingleScalarResult();
        
        switch($_format)
        {
            case 'json':
                $response = new NormalizedResponse();
                $response->setResults($comments);
                $response->setLimit($limit);
                $response->setTotal($count);
                
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
        
        $em = $this->getDoctrine()->getManager();
        
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
        
        //SECURITY CHECK
        if (!$this->get('security.context')->getToken()->getUser() instanceof User)
        {
                                                //TODO: i18n
            throw new AccessDeniedException('Vous devez être un enregistré pour ajouter un commentaire');
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $serializedJson = $request->get('entity', null);
        
        if ($serializedJson === null)
        {
            $r = new Response("Aucune entitée envoyée"); //TODO: i18n
            $r->setStatusCode(406, 'bad json');
            return $r;
        }

        $serializer = $this->get('progracqteurWikipedaleSerializer');
        
        $comment = $serializer->deserialize($serializedJson, 
                NormalizerSerializerService::COMMENT_TYPE, 
                $_format);
        
        //SECURITY CHECK
        if ($comment->getType() === Comment::TYPE_MODERATOR_MANAGER) {
            if ($this->get('security.context')->isGranted(User::ROLE_COMMENT_MODERATOR_MANAGER)) {
                //ok, may add a comment
            } else {
                return $this->getNotAuthorizedResponse("security.not_authorized.comment_of_type ".$comment->getType());
            }
        }
        
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
        
        

        $errors = $this->get('validator')->validate($comment);
        
        if ($errors->count() > 0) {
            
            if ($_format === 'json')
                $str = array();
            else
                $str = '';
            
            foreach($errors as $error) {
                if ($_format === 'json')
                    $str[] = $error->getMessage();
                else
                    $str .= $error->getMessage().' ';
            }
            
            if ($_format === 'json')
                $str = json_encode($str);
            
            $r = new Response($str);
            $r->setStatusCode(400);
            return $r;
        }

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
        $em = $this->getDoctrine()->getManager();
        
        $comment = $em->getRepository('ProgracqteurWikipedaleBundle:Model\\Comment')
                ->find($commentId);
        
        if ($comment === null)
        {
            throw $this->createNotFoundException("comment with id $commentId not found");
        }
        
        switch ($comment->getType()) {
            case Comment::TYPE_MODERATOR_MANAGER:
                if ( ! $this->get('security.context')->isGranted(User::ROLE_COMMENT_MODERATOR_MANAGER)) {
                    throw new AccessDeniedException('security.comment.must_have_role '.User::ROLE_COMMENT_MODERATOR_MANAGER);
                }
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
    
    private function getNotAuthorizedResponse($text = "security.not_allowed") {
        $r = new Response($text);
        $r->setStatusCode(403);
        return $r;
    }
    
    
    
}

