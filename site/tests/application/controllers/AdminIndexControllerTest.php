<?php
/**
 * TestController
 * 
 * @author
 * @version 
 */
class AdminIndexControllerTest extends ControllerTestCase
{ 
    public function testCanDispatchDefaultIndex()
    {
    	$this->dispatch('/admin/');
    	$this->assertModule('admin');
    	$this->assertController('index');
    	$this->assertAction('index');
    }
}