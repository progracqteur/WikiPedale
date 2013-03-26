<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model;

use Doctrine\ORM\Mapping as ORM;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;
use Progracqteur\WikipedaleBundle\Entity\Model\Comment;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Progracqteur\WikipedaleBundle\Resources\Container\Address;
use Progracqteur\WikipedaleBundle\Entity\Management\UnregisteredUser;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeableInterface;
use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\Common\PropertyChangedListener;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeService;
use Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceStatus;
use Symfony\Component\Validator\ExecutionContext;
use Progracqteur\WikipedaleBundle\Entity\Model\Category;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;
/**
 * Progracqteur\WikipedaleBundle\Entity\Model\Place
 */
class Place implements ChangeableInterface, NotifyPropertyChanged
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Progracqteur\WikipedaleBundle\Resources\Container\Address $address
     */
    private $address;

    /**
     * @var Progracqteur\WikipedaleBundle\Resources\Geo\Point $geom
     */
    private $geom;



    /**
     * @var datetime $createDate
     */
    private $createDate;

    /**
     * @var int $nbVote
     */
    private $nbVote = 0;

    /**
     * @var int $nbComm
     */
    private $nbComm = 0;

    /**
     * @var Progracqteur\WikipedaleBundle\Resources\Container\Hash $infos
     */
    private $infos;
    
    /**
     *
     * @var boolean
     */
    private $accepted = true;

    /**
     * @var Progracqteur\WikipedaleBundle\Entity\Management\User
     */
    private $creator;
    
    private $creatorUnregisteredProxy;
    
    /**
     * @var Progracqteur\WikipedaleBundle\Entity\Model\Photo
     */
    private $photos;
    /**
     * @var string $description
     */
    private $description = '';
    
    private $nbPhoto = 0;
    
    private $statusZone = 0;
    
    private $statusBicycle = 0;
    
    private $lastUpdate;
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking 
     */
    private $changeset = null;
    
    /**
     * pour la persistance
     * @var \Doctrine\Common\Collections\ArrayCollection 
     */
    private $changesets;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $category;
    
    /**
     *
     * @var \Progracqteur\WikipedaleBundle\Entity\Management\Group
     */
    private $manager;
    
    /**
     *
     * @var \Progracqteur\WikipedaleBundle\Entity\Place\PlaceType 
     */
    private $type;
    
    /**
     * comment for moderators of the system
     * 
     * @var string
     */
    private $moderatorComment = '';
            
    
    private $_listeners = array();

    public function __construct()
    {
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection();
        $d = new \DateTime();
        $this->setLastUpdate($d);
        $this->setCreateDate($d);
        $this->infos = new Hash();
        $this->address = new Address();
        $this->changesets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->getChangeset()->addChange(ChangeService::PLACE_CREATION, null);
        $this->category = new \Doctrine\Common\Collections\ArrayCollection();
        
        //initialize the placeStatuses
        $this->infos->placeStatuses = new Hash();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    private function change($propName, $oldValue, $newValue) {
/* FIXME: il semble que les chagnements de la description (et de l'adresse ?)
 * ne soient 
 * pas pris en compte lorsque le trackingPolicy est sur Notify
 */
        if ($this->_listeners) {
            foreach ($this->_listeners as $listener) {
                $listener->propertyChanged($this, $propName, $oldValue, $newValue);
            }
        }
        
        
        $oldUpdate = $this->getLastUpdate();
        $this->setLastUpdateNow();
        
        //change date update
        if ($this->_listeners) {
            foreach ($this->_listeners as $listener) {
                $listener->propertyChanged($this, 'lastUpdate', $oldUpdate, $this->getLastUpdate());
            }
            
        }  
    }

    /**
     * Set address
     *
     * @param Progracqteur\WikipedaleBundle\Resources\Container\Address $adress
     */
    public function setAddress(Address $address)
    {
        if (! $address->equals($this->address))
        {
            $this->change('address', $this->address, $address);
            $this->address = $address;
            $this->getChangeset()->addChange(ChangeService::PLACE_ADDRESS, $address);
        }
        
    }

    /**
     * Get adress
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Container\Hash 
     */
    public function getAddress()
    {
        if ($this->address != null) 
        {
            return $this->address;
        } else 
        {
            $addr =  new Address();
            $addr->setRoad('Adresse inconnue');
            return $addr;
        }
    }

    /**
     * Set geom
     *
     * @param Progracqteur\WikipedaleBundle\Resources\Geo\Point $geom
     */
    public function setGeom(Point $geom)
    {
        if ($this->getGeom() === null)
        {
            $this->geom = $geom;
            return;
        }
        
        if (
                $this->getGeom()->getLat() != $geom->getLat()
                && $this->getGeom()->getLon() != $geom->getLon()) 
        {
            $this->change('geom', $this->geom, $geom);
            $this->geom = $geom;
            $this->getChangeset()->addChange(ChangeService::PLACE_GEOM, $geom );
        }
    }

    /**
     * Get geom
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Geo\Point 
     */
    public function getGeom()
    {
        return $this->geom;
    }




    /**
     * Set createDate
     *
     * @param datetime $createDate
     */
    private function setCreateDate($createDate)
    {
        $this->change('createDate', $this->createDate, $createDate);
        $this->createDate = $createDate;
    }

    /**
     * Get createDate
     *
     * @return datetime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }


    /**
     * Get nbVote
     *
     * @return int 
     */
    public function getNbVote()
    {
        return $this->nbVote;
    }



    /**
     * Get nbComm
     *
     * @return int 
     */
    public function getNbComm()
    {
        return $this->nbComm;
    }

    /**
     * Set infos
     *
     * @param Progracqteur\WikipedaleBundle\Resources\Container\Hash $infos
     * @deprecated
     */
    private function setInfos(Hash $infos)
    {
        $this->infos = $infos;
        $this->setLastUpdateNow();
    }

    /**
     * Get infos
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Container\Hash 
     */
    private function getInfos()
    {
        return $this->infos;
    }

    /**
     * Set creator
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Management\User $creator
     */
    public function setCreator(\Progracqteur\WikipedaleBundle\Entity\Management\User $creator)
    {
        if ($this->_getCreator() === null OR  !($creator->equals($this->_getCreator())))
        {
            if ($creator instanceof UnregisteredUser)
            {
                if ($this->creator !== null)
                {
                    $this->change('creator', $this->creator, null);
                    $this->creator = null;
                }


                $old = clone($this->getInfos());
                $this->infos->creator = $creator->toHash();
                $this->creatorUnregisteredProxy = $creator;

                $this->change('infos', $old, $this->getInfos());
                $this->getChangeset()->addChange(ChangeService::PLACE_CREATOR, $creator);

            } else {
                $this->change('creator', $this->creator, $creator);
                $this->creator = $creator;
                $this->getChangeset()->addChange(ChangeService::PLACE_CREATOR, $creator);
                //TODO : si un unregistreredCreator existe, il faut l'enlever
            }
        }
    }

    /**
     * Get creator
     *
     * @return Progracqteur\WikipedaleBundle\Entity\Management\User 
     */
    public function getCreator()
    {
        return $this->_getCreator();
        
        //TODO : modifier les lignes suivantes, devenues inutiles
        
        if ($creator === null)
        {
            throw new \Exception('Aucun créateur enregistré');
        } else {
            return $creator;
        }
        
        
    }
    
    /**
     * variable used as proxy by the getStatuses function
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection()|null
     */
    private $proxyStatuses = null;
    
    private function initializeProxyStatuses()
    {
        if ($this->proxyStatuses === null)
        {
            $this->proxyStatuses = new \Doctrine\Common\Collections\ArrayCollection();
            
            foreach ($this->infos->placeStatuses->toArray() as $type => $value)
            {
                $status = new PlaceStatus();
                $status->setType($type)->setValue($value);
                $this->proxyStatuses->add($status);
            }
        }
        
        /*if (! $this->infos->has('placeStatuses'))
        {
            $this->infos->placeStatuses = new Hash();
        }*/
    }
    
    /**
     * return the statuses
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection()
     */
    public function getStatuses()
    {
        $this->initializeProxyStatuses();
        
        return $this->proxyStatuses;
    }
        
    /**
     * Add a new status to the class, and retrieve old status 
     * with same type.
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceStatus $status
     * @return \Progracqteur\WikipedaleBundle\Entity\Model\Place
     */
    public function addStatus(PlaceStatus $status)
    {
        $this->initializeProxyStatuses(); 
        
        $old = clone($this->infos);
 
        foreach ($this->getStatuses() as $key => $oldStatus)
        {
            if ($status->getType() ===  $oldStatus->getType())
            {
                if ($status->getValue() !== $oldStatus->getValue())
                {
                    $this->proxyStatuses->remove($key);
                    $this->infos->placeStatuses->remove($status->getType());
                } else {
                    return $this;
                }
                
                break;
            }
        }
        
        $this->proxyStatuses->add($status);
        $this->infos->placeStatuses->__set($status->getType(), $status->getValue());
        
        $this->change('infos', $old, $this->infos);
        
        $this->getChangeset()->addChange(ChangeService::PLACE_STATUS, $status);
        
        $this->proxyCountStatusChanges++;

        return $this;
    }
    
    /**
     * Remove completely the statuses equals of the given status
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceStatus $status
     * @return \Progracqteur\WikipedaleBundle\Entity\Model\Place
     */
    public function removeStatus(PlaceStatus $status)
    {
        $this->initializeProxyStatuses();
        
        foreach ($this->getStatuses() as $key => $inStatus)
        {
            if ($status->equals($inStatus))
            {
                $old = clone($this->infos);
                $this->proxyStatuses->remove($key);
                $this->infos->placeStatuses->remove($status->getType());
                $this->change('infos', $old, $this->infos);
                $this->proxyCountStatusChanges++;
                break;
            }
        }
        return $this;
    }
    
    /**
     * Currently, we must update only ONE change at a time. 
     * 
     * This is necessary for the changes to be kept into changesets.
     * 
     * @var int 
     */
    private $proxyCountStatusChanges = 0;
    
    //TODO: allow changeset to record more than one change of status
    
    //TODO: reset proxyCountStatusChange after update
    
    /**
     * check if value of statuses are valid.
     * 
     * the verifiation of validity of statuses's types are delegated 
     * to the controller.
     * 
     * function used by validation service.
     * 
     * @return boolean
     */
    public function isStatusesValid(ExecutionContext $context)
    {
        $this->initializeProxyStatuses();

        foreach($this->getStatuses() as $status)
        {
            
            if ($status->getValue() >= -1 && $status->getValue() <= 3)
            {
                
            } else 
            {
                $propertyPath = $context->getPropertyPath() . '.status';
                $context->setPropertyPath($propertyPath);
                $context->addViolation('place.validation.message.status.valueNotCorrect', array(), null);
            }
        }
        
        return true;
        
    }
    
    /**
     * Currently, we must update only ONE change at a time. 
     * 
     * This is necessary for the changes to be kept into changesets.
     * 
     * This function check if only one change has been made and is used
     * for validation
     * @return boolean
     */
    public function hasOnlyOneChange(ExecutionContext $context)
    {
        if ($this->proxyCountStatusChanges <= 1)
            return true;
        else
        {
            $context->addViolationAtSubPath('status', 'place.validation.message.onlyOneStatusAtATime', array(), null);
        }
            
    }

    
    
    
    /**
     * return the common way to name a place
     * (currently the name of the street)
     * 
     * @return string
     */
    public function getLabel()
    {
        $l =  $this->getAddress()->getRoad();
        
        if (strlen($l) < 2)
        {
            $l = "Sans label";
        }
        
        return $l;
    }
    
    /**
     * transform the place into a string displayable on UI
     * @return string
     */
    public function __toString() {
        return $this->getLabel();
    }
    
    /**
     * REnvoie le créateur
     * Méthode privée utilisée pour éviter l'exception de la méthode
     * publique getCreator. Utilisée dans setCreator().
     * 
     * @return Progracqteur\WikipedaleBundle\Entity\Management\User 
     * @deprecated
     */
    private function _getCreator()
    {
        if (!is_null($this->creator))
        {
            return $this->creator;
        } 
        elseif (!is_null($this->creatorUnregisteredProxy))
        {
            return $this->creatorUnregisteredProxy;
        } 
        elseif ($this->infos->has('creator'))
        {
            $u = UnregisteredUser::fromHash($this->infos->creator);
            $this->creatorUnregisteredProxy = $u;
            return $u;
        } 
        else 
        {
            return null;
        }
    }

    /**
     * Get photos
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPhotos()
    {
        return $this->photos;
    }
    
    public function getNbPhoto()
    {
        return $this->nbPhoto;
    }
    
    public function increaseComment()
    {
        $this->nbComm++;
        $this->change('nbComm', ($this->nbComm -1 ), $this->nbComm);
        $this->getChangeset()->addChange(ChangeService::PLACE_ADD_COMMENT, 1);
    }
    
    public function increaseVote()
    {
        $this->nbVote++;
        $this->change('nbVote', ($this->nbVote - 1), $this->nbVote);
        $this->getChangeset()->addChange(ChangeService::PLACE_ADD_VOTE, 1);
    }
    
    private function increasePhoto()
    {
        $this->nbPhoto++;
        $this->change('nbPhoto', ($this->nbPhoto -1), $this->nbPhoto);
    }
    
    private function decreasePhoto()
    {
        $this->nbPhoto--;
        $this->change('nbPhoto', ($this->nbPhoto +1), $this->nbPhoto);
        //TODO: implémenter tracking policy
                
    }

 

    /**
     * Add photos
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Model\Photos $photos
     */
    public function addPhotos(\Progracqteur\WikipedaleBundle\Entity\Model\Photo $photos)
    {
        //TODO: implémenter le tracking policy pour les photos
        $this->photos[] = $photos;
        $this->increasePhoto();
        $this->getChangeset()->addChange(ChangeService::PLACE_ADD_PHOTO, $photos);
    }
    
    public function removePhotos(\Progracqteur\WikipedaleBundle\Entity\Model\Photo $photo)
    {
        //TODO: compléter la fonction removePhoto
        $this->decreasePhoto();
        $this->getChangeset()->addChange(ChangeService::PLACE_REMOVE_PHOTO, $photo->getFileName());
    }
    


    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $description = trim($description);
        if ($this->description !== $description )
        {
            $this->change('description', $this->description, $description);
            $this->description = $description;
            $this->getChangeset()->addChange(ChangeService::PLACE_DESCRIPTION, $description);
        }
        
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    public function getStatusZone() 
    {
        return $this->statusZone;
    }
    
    public function getStatusBicycle()
    {
        return $this->statusBicycle;
    }
    
    public function setModeratorComment($comment) 
    {
        if ($this->moderatorComment !== $comment)
        {
            $this->change('moderatorComment', $this->moderatorComment, $comment);
            $this->moderatorComment = $comment;
            $this->getChangeset()->addChange(
                    ChangeService::PLACE_MODERATOR_COMMENT_ALTER, 
                    $comment);
        }
        
        return $this;
    }
    
    public function getModeratorComment()
    {
        return $this->moderatorComment;
    }
    
    public function setAccepted($accepted)
    {
        if ($this->accepted != $accepted)
        {
            $this->change('accepted', $this->accepted, $accepted);
            $this->accepted = $accepted;
            $this->getChangeset()->addChange(
                    ChangeService::PLACE_ACCEPTED, 
                    $accepted);
        }
    }
    
    public function isAccepted()
    {
        return $this->accepted;
    }
    
    public function setStatusBicycle($status)
    {
        if ($this->statusBicycle != $status)
        {
            $this->change('statusBicycle', $this->statusBicycle, $status);
            $this->statusBicycle = $status;
            $this->getChangeset()->addChange(ChangeService::PLACE_STATUS_BICYCLE, $status);
        }
    }
    
    public function setStatusZone($status)
    {
        if ($this->statusZone != $status)
        {
            $this->change('statusZone', $this->statusZone, $status);
            $this->statusZone = $status;
            $this->getChangeset()->addChange(ChangeService::PLACE_STATUS_Zone, $status);
        }
    }
    
    private function setLastUpdate(\DateTime $d)
    {
        $this->lastUpdate = $d;
    }
    
    private $proxyLastUpdate = false;
    
    private function setLastUpdateNow()
    {
        if ($this->proxyLastUpdate === false)
        {
            $this->lastUpdate = new \DateTime();
            $this->proxyLastUpdate = true;
        }
        
        //be careful: do not add method $this->change you risk recursive operation
                
    }
    
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }
    
    public function setManager(Group $manager = null)
    {
        
        if ($manager === null)
        {
            return $this->removeManager();
        }
        
        if ($this->getManager() === null)
        {
            if ($this->getChangeset()->isCreation())
            {
                $this->manager = $manager;
                $this->getChangeset()
                        ->addChange(ChangeService::PLACE_MANAGER_ADD, $manager);
            } else {
                $this->change('manager', $this->manager, $manager);
                $this->getChangeset()->addChange(ChangeService::PLACE_MANAGER_ALTER, $manager);
            } 
        } elseif ($this->getManager()->getId() !== $manager->getId())
        {
            $this->change('manager', $this->manager, $manager);
            $this->getChangeset()->addChange(ChangeService::PLACE_MANAGER_ALTER, $manager);
        }
        
        return $this;
    }
    
    public function removeManager()
    {
        if ($this->getManager() !== null)
        {
            $this->change('manager', $this->manager, $manager);
            $this->getChangeset()->addChange(ChangeService::PLACE_MANAGER_REMOVE, null);
        }
        
        return $this;
    }
    
    /**
     * 
     * 
     * @return \Progracqteur\WikipedaleBundle\Entity\Management\Group
     */
    public function getManager()
    {
        return $this->manager;
    }
    
    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceType $type
     * @return \Progracqteur\WikipedaleBundle\Entity\Model\Place
     */
    public function setType(Place\PlaceType $type)
    {
        if ($this->getType() === null)
        {
            $this->type = $type;
            $this->getChangeset()->addChange(ChangeService::PLACE_PLACETYPE_ALTER, $type);
            
        } elseif ($this->getType()->getId() !== $type->getId())
        {
            $this->change('type', $this->type, $type);
            $this->getChangeset()->addChange(ChangeService::PLACE_PLACETYPE_ALTER, $type);
        }
        
        
        return $this;
    }
    
    /**
     * 
     * @return \Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceType
     */
    public function getType()
    {
        return $this->type;
    }
    
    public function setChecked()
    {
        $this->getChangeset()->addChange(ChangeService::PLACE_CHECK, true);
    }
    /**
     * return the changeset made since the entity was created or 
     * retrieved from the database.
     * 
     * @return \Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking
     */
    public function getChangeset() {
        
        if ($this->changeset === null)
        {
            $this->changeset = new Place\PlaceTracking($this);
            $this->changesets->add($this->changeset);
        }
        
        return $this->changeset;
    }

    public function addPropertyChangedListener(PropertyChangedListener $listener) {
        $this->_listeners[] = $listener;
    }
   
    
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection 
     */
    private $proxyAddCategory = null;

    /**
     * Add category
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Model\Category $category
     * @return Place
     */
    public function addCategory(Category $category)
    {
        $this->category[] = $category;
        if ($this->proxyAddCategory === null)
        {
            $this->proxyAddCategory = new \Doctrine\Common\Collections\ArrayCollection();
        }
        
        $this->proxyAddCategory->add($category);
        
        $this->getChangeset()->addChange(ChangeService::PLACE_ADD_CATEGORY, $this->proxyAddCategory);
        
        return $this;
    }

    /**
     * Get category
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    
    private $proxyRemoveCagory = null;
    
    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\Category $category
     * @return \Progracqteur\WikipedaleBundle\Entity\Model\Place
     */
    public function removeCategory(Category $category)
    {
        foreach ($this->category as $key => $categoryRecorded)
        {
            if ($categoryRecorded->getId() === $category->getId())
            {
                $this->category->remove($key);
                if ($this->proxyRemoveCagory === null)
                {
                    $this->proxyRemoveCagory = new \Doctrine\Common\Collections\ArrayCollection();
                }
                $this->proxyRemoveCagory->add($category);
                
                $this->getChangeset()
                        ->addChange(ChangeService::PLACE_REMOVE_CATEGORY, 
                                $this->proxyRemoveCagory);
            }
        }
        
        return $this;
    }
    
    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\Category\Progracqteur\WikipedaleBundle\Entity\Model\Category $category
     * @return boolean
     */
    public function hasCategory(Category $category)
    {
        foreach($this->category as $cat)
        {
            if ($cat->getId() == $category->getId())
                return true;
        }
        return false;
    }
    
    /**
     * check if the categories added to the place are valid. 
     * 
     * Until now: no categories with children are accepted !
     * 
     * @param \Symfony\Component\Validator\ExecutionContext $context
     */
    public function isCategoriesValid(ExecutionContext $context)
    {
        foreach($this->getCategory() as $cat)
        {
            if ($cat->hasChildren())
            {
                $context->addViolationAtSubPath('category', 'validation.place.category.have_children', array(), null);
                return;
            }
        }
    }
    
    public function isManagerValid(ExecutionContext $context)
    {
        if ($this->getManager() !== null 
                && $this->getManager()->getType() !== Group::TYPE_MANAGER )
        {
            $context->addViolationAtSubPath('manager', 'validation.place.manager.group_is_not_type_manager', 
                    array(), $this->getManager());
        }
    }
}