<?php

namespace Progracqteur\WikipedaleBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

//require Kernel
require_once __DIR__.'/../../../../../app/AppKernel.php';

/**
 * Description of SlugControllerTest
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class SlugControllerTest extends WebTestCase{
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Services\SlugService 
     */
    private $service;
    
    private $_kernel;
    
    public function __construct() {
        parent::__construct();
        
        if ($this->_kernel === null){
            $this->_kernel = new \AppKernel('dev', true);
            $this->_kernel->boot(); 
        }
        
        $this->service = $this->_kernel->getContainer()->get('progracqteur.wikipedale.slug');
    }
    
    public function testSpace()
    {
        $string = $this->service->slug('test test');
        
        //echo "retour est : ".$string."fin \n";
        $this->assertEquals('test-test', $string);
    }
    
    public function testAccent()
    {
        $this->general('testétest', 'testetest');
    }
    
    public function testTrim()
    {
        $this->general(' test ', 'test');
    }
    
    public function testA()
    {
        $this->general('àâäá', 'aaaa');
    }
    
    public function testU()
    {
        $this->general('uùüû', 'uuuu');
    }
    
    public function testTiret()
    {
        $this->general('Braine-le-chateau', 'braine-le-chateau');
    }
    
    public function testApostrophe()
    {
        $this->general('Braine l\'alleud', 'braine-lalleud');
    }
    
    private function general($string, $expectedSlug)
    {
        $result = $this->service->slug($string);
        $this->assertEquals($expectedSlug, $result);
    }
    
}

