<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management;

use Doctrine\ORM\Mapping as ORM;

/**
 * Progracqteur\WikipedaleBundle\Entity\Management\City
 */
class City
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
     * Set id
     *
     * @param integer $id
     * @return City
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
     * @return City
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
     * @return City
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
     * @return City
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
     * @return City
     */
    public function setPolygon(\polygon $polygon)
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
}