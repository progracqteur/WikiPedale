<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedResponse;

/**
 * Description of PhotoController
 *
 * @author julien [arobase] fastre POINT info
 */
class PhotoController extends Controller 
{
    public function getPhotoByPlaceAction($_format, $placeId, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $place = $em->getRepository("ProgracqteurWikipedaleBundle:Model\\Place")->find($placeId);
        
        if ($place === null)
        {
            throw $this->createNotFoundException("La place $placeId n'a pas été trouvée");
        }
        
        $q = $em->createQuery("SELECT ph from ProgracqteurWikipedaleBundle:Model\\Photo ph 
            where ph.place = :place and ph.published = true")
                ->setParameter('place', $place);
        
        $photos = $q->getResult();
        
        switch($_format)
        {
            case 'json':
                $response = new NormalizedResponse();
                $response->setResults($photos);
                
                $serializer = $this->get('progracqteurWikipedaleSerializer');
                $string = $serializer->serialize($response, $_format);
                
                return new Response($string);
                break;
            default:
                throw new \Exception("le format $_format est inconnu");
        }
    }
    
    public function newAction($placeId, $_format, Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            throw new AccessDeniedException('Vous devez être un administrateur pour modifier une image');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $place = $em->getRepository("ProgracqteurWikipedaleBundle:Model\\Place")->find($placeId);
        
        if ($place === null)
        {
            throw $this->createNotFoundException("La place $placeId n'a pas été trouvée");
        }
        
        $photo = $this->get('progracqteurWikipedalePhotoService')->createPhoto();
        $photo->setPlace($place);
        
        $security = $this->get('security.context');
        
        if ($security->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $photo->setCreator($security->getToken()->getUser());
        }
        
        $formPhotoType = $this->get('progracqteurWikipedale.form.type.photo');
        $formPhotoType->isForANewPhoto(true);
        
        $form = $this->createForm($formPhotoType, $photo);
        
        
        if ($request->getMethod() === 'POST')
        {
            $form->bindRequest($request);
            
            $uploadedFile = $form['file']->getData();
            
            if (!$uploadedFile->isValid())
            {
                throw new \Exception("Erreur d'envoi de fichier. Vérifiez la taille du fichier");
            }
            
            if ($form->isValid()) //TODO créer une formulaire de validation
            {
                $em->persist($photo);
                $em->flush();
                
                $this->get('session')->setFlash('notice', "Votre photo a été correctement enregistrée.");
                
                return $this->redirect($this->generateUrl('wikipedale_photo_update', array(
                    'photoId' => $photo->getId(),
                    '_format' => $_format
                ))); //TODO renvoyer vers le chemin adéquat
            }
        }
 
        
        return $this->render('ProgracqteurWikipedaleBundle:Photo:form.html.twig', array(
            'form' => $form->createView()
        ));
        
        
    }
    
    public function updateAction($photoId, $_format, Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $this->get('session')->setFlash('notice', "Vous devez être un administrateur pour modifier une image");
            throw new AccessDeniedException('Vous devez être authentifié pour modifier une image');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $photo = $em->getRepository('ProgracqteurWikipedaleBundle:Model\Photo')->find($photoId);
        
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
            
            $uploadedFile = $form['file']->getData();
            if ($uploadedFile !== null && !$uploadedFile->isValid())
            {
                throw new \Exception("Le fichier envoyé n'est pas valide");
            }
            
            if ($form->isValid())
            {
                $em->flush();
                
                $this->get('session')->setFlash('notice', "La photo a été mise à jour");
                
                return $this->redirect($this->generateUrl('wikipedale_photo_update', array(
                    'photoId' => $photo->getId(),
                    '_format' => $_format
                    
                    )));
                
            }
        }
        
        return $this->render('ProgracqteurWikipedaleBundle:Photo:form.html.twig', array(
            'form' => $form->createView(),
            'photo' => $photo
        ));
        
    }
}

