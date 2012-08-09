<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Entity\Model\Photo;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Progracqteur\WikipedaleBundle\Form\Model\PhotoType;

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
            $this->createNotFoundException("La place $placeId n'a pas été trouvée");
        }
        
        $q = $em->createQuery("SELECT ph from ProgracqteurWikipedaleBundle:Model\\Photo ph where ph.place = :place")
                ->setParameter('place', $place);
        
        $photos = $q->getResult();
        
        $response = "Nombre de photos ".count($photos)." 
            ";
        
        foreach ($photos as $photo)
        {
            $response.= $photo->getFile();
        }
        
        return new Response($response);
    }
    
    public function newAction($placeId, Request $resquest)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $place = $em->getRepository("ProgracqteurWikipedaleBundle:Model\\Place")->find($placeId);
        
        if ($place === null)
        {
            $this->createNotFoundException("La place $placeId n'a pas été trouvée");
        }
        
        $photo = $this->get('progracqteurWikipedalePhotoService')->createPhoto();
        
        $security = $this->get('security.context');
        
        if ($security->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $photo->setCreator($security->getToken()->getUser());
        }
        
        $form = $this->createForm($this->get('progracqteurWikipedale.form.type.photo'), $photo);
        $form->remove('place');
        
        return $this->render('ProgracqteurWikipedaleBundle:Photo:form.html.twig', array(
            'form' => $form->createView()
        ));
        
        
    }
}

