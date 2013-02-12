<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management;

use Doctrine\ORM\Mapping as ORM;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;

/**
 * Progracqteur\WikipedaleBundle\Entity\Management\Zone
 */
class Zone
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var string $codeProvince
     */
    private $codeProvince;

    /**
     * @var polygon $polygon
     */
    private $polygon;
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Geo\Point 
     */
    private $center;
    
    /**
     *
     * @var string
     */
    private $type;
    
    /**
     * Type of zone "city"
     * 
     * @var string
     */
    const TYPE_CITY = 'city';


    /**
     * Set id
     *
     * @param integer $id
     * @return Zone
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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

    /**
     * Set name
     *
     * @param string $name
     * @return Zone
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Zone
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set codeProvince
     *
     * @param string $codeProvince
     * @return Zone
     */
    public function setCodeProvince($codeProvince)
    {
        $this->codeProvince = $codeProvince;
        return $this;
    }

    /**
     * Get codeProvince
     *
     * @return string 
     */
    public function getCodeProvince()
    {
        return $this->codeProvince;
    }

    /**
     * Set polygon
     *
     * @param polygon $polygon
     * @return Zone
     */
    public function setPolygon($polygon)
    {
        $this->polygon = $polygon;
        return $this;
    }

    /**
     * Get polygon
     *
     * @return polygon 
     */
    public function getPolygon()
    {
        return $this->polygon;
    }
    
    /**
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Geo\Point 
     */
    public function getCenter()
    {
        return $this->center;
    }
    
    public function __toString() {
        return $this->getName().' ('.$this->getType().')';
    }
    
    /**
     * Type may be : 
     * 
     * - city
     * 
     * 
     * 
     * @return type The type of the zone
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * Set the type of the Zone.
     * 
     * 
     * @param type $type
     * @return \Progracqteur\WikipedaleBundle\Entity\Management\Zone
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }
}