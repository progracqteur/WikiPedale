<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * Progracqteur\WikipedaleBundle\Entity\Management\Group
 */
class Group extends BaseUser
{

    /**
     * @var polygon $polygon
     */
    private $polygon;

    /**
     * @var Progracqteur\WikipedaleBundle\Management\Notation
     */
    private $notation;

    /**
     * Set polygon
     *
     * @param polygon $polygon
     * @return Group
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
     * Set notation
     *
     * @param Progracqteur\WikipedaleBundle\Management\Notation $notation
     * @return Group
     */
    public function setNotation(\Progracqteur\WikipedaleBundle\Management\Notation $notation = null)
    {
        $this->notation = $notation;
        return $this;
    }

    /**
     * Get notation
     *
     * @return Progracqteur\WikipedaleBundle\Management\Notation 
     */
    public function getNotation()
    {
        return $this->notation;
    }
}