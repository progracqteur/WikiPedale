<?php
namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedResponse;
use Progracqteur\WikipedaleBundle\Entity\Management\User;

/**
 * Description of CommentController
 *
 * @author marcducobu [arobase] gmail POINT com & julien [arobase] fastre POINT info
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
    
    public function newAction($placeId, $_format, Request $request)
    {
        if (!$this->get('security.context')->getToken()->getUser() instanceof User)
        {
            throw new AccessDeniedException('Vous devez être un enregistré pour ajouter un commentaire');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $place = $em->getRepository("ProgracqteurWikipedaleBundle:Model\\Place")->find($placeId);
        
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
        
        $comment = $serializer->deserialize($serializedJson, NormalizerSerializerService::COMMENT_TYPE, $_format);

        $user = $this->get('security.context')->getToken()->getUser();
        if ($user instanceof User) { //si user is logger
            $comment.setCreator($user);
        }
        else {
            throw new \Exception("Il faut être connecté pour ajouter un commentaire");
        }

        if (! $this->get('security.context')->isGranted(User::ROLE_NOTATION)) {
            throw new \Exception("Vous n'avez pas le droit d'ajouter un commentaire");
        }

        print "plus de verif pour les droits";

        $em->persist($comment);
        $em->flush();
    }
    
    
    public function viewAction($fileNameP, $photoType, $_format, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $photo = $em->getRepository('ProgracqteurWikipedaleBundle:Model\Photo')
                ->findOneBy(array('file' => $fileNameP.'.'.$photoType));
        
        if ($photo === null)
        {
            throw $this->createNotFoundException("La photo demandée n'a pas été trouvée");
        }
        
        return $this->render('ProgracqteurWikipedaleBundle:Photo:view.html.twig', array(
            'photo' => $photo,
        ));
    }
    
    public function updateAction($fileNameP, $photoType, $_format, Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_NOTATION'))
        {
            $this->get('session')->setFlash('notice', "Vous devez être un administrateur pour modifier une image");
            throw new AccessDeniedException('Vous devez être authentifié pour modifier une image');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $photo = $em->getRepository('ProgracqteurWikipedaleBundle:Model\Photo')
                ->findOneBy(array('file' => $fileNameP.'.'.$photoType));
        
        if ($photo === null)
        {
            throw $this->createNotFoundException("La photo demandée n'a pas été trouvée");
        }
        
        $form = $this->createForm(
                    $this->get('progracqteurWikipedale.form.type.photo'), $photo
                );
        
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);
            $photo->setPhotoService($this->get('progracqteurWikipedalePhotoService'));
            
            $uploadedFile = $form['file']->getData();
            if ($uploadedFile != null && $uploadedFile->isValid())
            {
                $error = $uploadedFile->getError();
                throw new \Exception("Le fichier envoyé n'est pas valide. Erreur : $error dump : $dump");
            }
            
            if ($form->isValid())
            {
                $em->flush();
                
                $this->get('session')->setFlash('notice', "La photo a été mise à jour");
                
                return $this->redirect($this->generateUrl('wikipedale_photo_update', array(
                    'fileNameP' => $photo->getFileName(),
                    'photoType' => $photo->getPhotoType(),
                    '_format' => $_format
                    
                    )));
                
            }
        }
        
        return $this->render('ProgracqteurWikipedaleBundle:Photo:form.html.twig', array(
            'form' => $form->createView(),
            'photo' => $photo
        ));
        
    }
    
    private function file_upload_error_message($error_code) {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    } 
}

