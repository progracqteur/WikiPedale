<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Geo\BBox;
use Symfony\Component\HttpFoundation\Request;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedResponse;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedExceptionResponse;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Description of PlaceController
 *
 * @author Julien Fastré
 */
class PlaceController extends Controller {
    
    
    public function viewAction($_format, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $place = $em->getRepository('ProgracqteurWikipedaleBundle:Model\\Place')->find($id);
        
        if ($place === null OR $place->isAccepted() == false)
        {
            throw $this->createNotFoundException("L'endroit n'a pas été trouvé dans la base de donnée");
        }
        
        switch ($_format){
            case 'json':
                $normalizer = $this->get('progracqteurWikipedaleSerializer');
                $rep = new NormalizedResponse($place);
                $ret = $normalizer->serialize($rep, $_format);
                return new Response($ret);
                
                /* code mort
                $jsonencoder = new JsonEncoder();
                $serializer = new Serializer(array(new CustomNormalizer()) , array('json' => $jsonencoder));
		$rep = array('results' => array($place));
                $ret = $serializer->serialize($rep, $_format);
                return new Response($ret);*/
                break;
            
            case 'html' :
                return $this->render('ProgracqteurWikipedaleBundle:Place:view.html.twig', 
                        array(
                            'place' => $place
                        ));
                break;
        }
    }
    
    public function listByBBoxAction($_format, Request $request){
        
        throw new \Exception("cette fonction n'est plus fonctionnelle tant que la fonction covers n'a pas été adaptée");
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $BboxStr = $request->get('bbox', null);
        if ($BboxStr === null) {
            throw new \Exception('Fournissez un bbox');
        }
        
        $BboxArr = explode(',', $BboxStr, 4);
        
        foreach($BboxArr as $value){
            if (!is_numeric($value))
            {
                throw new \Exception("Le Bbox n'est pas valide : $BboxStr");
            }
        }
        
        
        
        
        $bbox = BBox::fromCoord($BboxArr[0], $BboxArr[1], $BboxArr[2], $BboxArr[3]);
        
        $p = $em->createQuery('SELECT p from ProgracqteurWikipedaleBundle:Model\\Place p 
                  where covers(:bbox, p.geom) = true and p.accepted = true')
                ->setParameter('bbox', $bbox->toWKT());
        
        $r = $p->getResult();
        
        switch($_format) {
            case 'json':
                $normalizer = $this->get('progracqteurWikipedaleSerializer');
                $rep = new NormalizedResponse($r);
                $ret = $normalizer->serialize($rep, $_format);
                return new Response($ret);
                break;
            case 'html':
                return new Response('Pas encore implémenté');
                
        }
        
        
    }
    
    public function listByCityAction($_format, Request $request)
    {
        
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $citySlug = $request->get('city', null);
        $citySlug = $this->get('progracqteur.wikipedale.slug')->slug($citySlug);
        
        if ($citySlug === null)
        {
            throw new \Exception('Renseigner une ville dans une variable \'city\' ');
        }
        
        $city = $em->getRepository('ProgracqteurWikipedaleBundle:Management\\City')
                ->findOneBy(array('slug' => $citySlug));
        
        if ($city === null)
        {
            throw $this->createNotFoundException("Aucune ville correspondant à $citySlug n'a pu être trouvée");
        }

        
        $p = $em->createQuery('SELECT p 
            from ProgracqteurWikipedaleBundle:Model\\Place p 
                
            where covers(:polygon, p.geom) = true and p.accepted = true')
                ->setParameter('polygon', $city->getPolygon());
        
        $r = $p->getResult();
        
        switch($_format) {
            case 'json':
                $normalizer = $this->get('progracqteurWikipedaleSerializer');
                $rep = new NormalizedResponse($r);
                
                $ret = $normalizer->serialize($rep, $_format);
                
                return new Response($ret);
                break;
            case 'html':
                return new Response('Pas encore implémenté');
                
        }
        
        
    }
    
    public function changeAction($_format, Request $request)
    {
        
        if ($request->getMethod() != 'POST')
        {
            throw new \Exception("Only post method accepted");
        }
        
        $serializedJson = $request->get('entity', null);
        
        if ($serializedJson === null)
        {
            throw new \Exception("Aucune entitée envoyée");
        }
        
        $serializer = $this->get('progracqteurWikipedaleSerializer');
        
        $place = $serializer->deserialize($serializedJson, NormalizerSerializerService::PLACE_TYPE, $_format);
        
        //ajoute l'utilisateur courant comme créateur si connecté
        if ($place->getId() == null && $this->get('security.context')->getToken()->getUser() instanceof \Progracqteur\WikipedaleBundle\Entity\Management\User)
        {
            $u = $this->get('security.context')->getToken()->getUser();
            $place->setCreator($u);
        }
        
        //ajoute l'utilisateur courant au changeset
        if ($this->get('security.context')->getToken()->getUser() instanceof Progracqteur\WikipedaleBundle\Entity\Management\User) //si utilisateur connecté
        {
            $place->getChangeset()->setAuthor($this->get('security.context')->getToken()->getUser());
        } elseif ($place->getChangeset()->isCreation()) { //si c'est une création, on ajoute l'utilisateur non enregistré qui a créé
            $user = new \Progracqteur\WikipedaleBundle\Entity\Management\UnregisteredUser();
            //Todo compléter
            $place->getChangeset()->setAuthor($user);
        }
        
        /**
         * @var Progracqteur\WikipedaleBundle\Resources\Security\ChangeService 
         */
        $securityController = $this->get('progracqteurWikipedaleSecurityControl');
        
        //try {
        //TODO implémenter une réponse avec code d'erreur en JSON
        $return = $securityController->checkChangesAreAllowed($place);
        /*} catch (ChangeException $exc) {
            $r = new NormalizedExceptionResponse($exc);
            $ret = $serializer->serialize($r, $_format);
            return new Response($ret);
        //}*/
        
        $validator = $this->get('validator');
        
        if ($place->getId() === null)
            $arrayValidation = array('Default', 'creation');
        else
            $arrayValidation = array('Default');
        
        $errors = $validator->validate($place, $arrayValidation);
        
        if ($errors->count() > 0)
        {
            $stringErrors = ''; $k = 0;
            foreach ($errors as $error)
            {
                $stringErrors .= $k.'. '.$error->getMessage();
            }
            
            throw new HttpException(403, 'Erreurs de validation : '.$stringErrors);
            
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($place);
        $em->flush();
               
        return $this->redirect(
                $this->generateUrl('wikipedale_place_view', 
                        array('id' => $place->getId(), 
                            '_format' => 'json',
                            'return' => $return,
                            'addUserInfo' => $request->get('addUserInfo', false))
                        )
                );
    }
}

