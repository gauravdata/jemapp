<?php
/**
 * TestController
 * 
 * @author
 * @version 
 */
require_once 'application/controllers/ControllerTestCase.php';
class IndexControllerTest extends ControllerTestCase
{ 
    public function testCanDoUnitTests()
    {
        $this->assertTrue(true);
    }
    
    public function testCanDispatchDefaultIndex()
    {
    	$this->dispatch('/');
    	$this->assertModule('default');
    	$this->assertController('index');
    	$this->assertAction('index');
    }
}