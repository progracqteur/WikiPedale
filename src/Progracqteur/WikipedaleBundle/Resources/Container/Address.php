<?php

namespace Progracqteur\WikipedaleBundle\Resources\Container;

/**
 * Description of Address
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class Address {
    private $city = null;
    private $administrative = null;
    private $county = null;
    private $state_district = null; 
    private $state = null;
    private $country = null;
    private $country_code = null;
    
    const CITY_DECLARATION = 'city';
    const ADMINISTRATIVE_DECLARATION = 'administrative';
    const COUNTY_DECLARATION = 'county';
    const STATE_DISTRICT_DECLARATION = 'state_district_declaration';
    const STATE_DECLARATION = 'state';
    const COUNTRY_DECLARATION = 'country';
    const COUNTRY_CODE_DECLARATION = 'country_code';
    
        
    
    public function setCity($city) {
        $this->city = $city;
    }
    
    public function setAdministrative($administrative) {
        $this->administrative = $administrative;
    }
    
    public function setStateDistrict($state_strict)
    {
        $this->state_district = $state_strict;
    }
    
    public function setCounty($county) {
        $this->county = $county;
    }
    
    public function setState($state) {
        $this->state = $state;
    }
    
    public function setCountry($country) {
        $this->country = $country;
    }
    
    public function setCountryCode($country_code) {
        $this->country_code = $country_code;
    }
    
    public function toArray(){
        return array(
          self::CITY_DECLARATION => $this->city,
          self::ADMINISTRATIVE_DECLARATION => $this->administrative,
          self::COUNTRY_CODE_DECLARATION => $this->country,
          self::COUNTY_DECLARATION => $this->county,
          self::STATE_DECLARATION => $this->state,
          self::STATE_DISTRICT_DECLARATION => $this->state_district
        );
    }
    
    public function getCity()
    {
        return $this->city;
    }
}

