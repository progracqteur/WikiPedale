<?php

namespace Progracqteur\WikipedaleBundle\Resources\Geo;

/**
 * Description of BBox
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class BBox {
    private $nord;
    private $sud;
    private $est;
    private $ouest;
    public static $SRID = '4326';
    
    private function __construct($nord, $est, $sud, $ouest) {
        $this->est = $est;
        $this->nord = $nord;
        $this->sud = $sud;
        $this->ouest = $ouest;
    }
    
    public function toArrayGeoJson() {
        $a['type'] = 'Polygon';
        
        $c[] = array($this->est, $this->nord);
        $c[] = array($this->ouest, $this->nord);
        $c[] = array($this->ouest, $this->sud);
        $c[] = array($this->est, $this->sud);
        $c[] = array($this->est, $this->nord);
        
        
        $a['coordinates'] = $c;
        
        return $a;
    }
    
    public function toGeoJson() {
        return json_encode($this->toArrayGeoJson());
    }
    
    public function toWKT() {
        $c = $this->toArrayGeoJson();
        $c = $c['coordinates'];
        
        $s = 'SRID='.self::$SRID.';POLYGON((';
        
        $i = 0;
        foreach ($c as $p) {
            $s.= $p[0].' '.$p[1].', ';
            if ($i != 4) {
                $s.= ', ';
            }
            $i++;
        }
        
        $s.= '))';
        
        return $s;
    }
    
    public static function fromCoord($nord, $est, $sud, $ouest) {
        return new BBox($nord, $est, $sud, $ouest);
    }
}

