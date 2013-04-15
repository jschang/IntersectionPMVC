<?php
/*
Copyright (C) 2012 Jon Schang

This file is part of IntersectionPMVC, released under the LGPLv3

IntersectionPMVC is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

IntersectionPMVC is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with IntersectionPMVC.  If not, see <http://www.gnu.org/licenses/>.
*/

include_once('PEAR.php');
include_once('PHPUnit/Framework.php');
include_once('PHPUnit/TextUI/TestRunner.php');

include_once(dirname(__FILE__).'/../../includes/Redesign.php');
IPMVC::loadClass('IPMVC_Model_Portlet');

class Model_PortletTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		IPMVC::loadClass('IPMVC_Model_IoCContainer');
		IPMVC::loadClass('IPMVC_Resource_Selector');
		IPMVC::loadClass('IPMVC_Resource_Selector_File');
		IPMVC::loadClass('IPMVC_Resource_File');
		
		$this->portletModel = new IPMVC_Model_Portlet();
		
		$this->selector = new IPMVC_Resource_Selector();
		$this->selector->setProtocolHandlers(array(
			'file'=>new IPMVC_Resource_Selector_File()
		));
		
		$this->ioc = new IPMVC_Model_IoCContainer( $this->selector->getResource("file://./PortletTest_IoC.xml") );
		
		$this->portletModel->setResourceSelector($this->selector);
		$this->portletModel->setIoCContainer($this->ioc);
	}
	public function testLoadPage() {
		$portlet = $this->portletModel->getPortlet( "file://./PortletTest.xml" );
		$this->assertTrue($portlet instanceof IPMVC_Portlet);
		$this->assertTrue($portlet->property1=='test_value');
		$this->assertTrue($portlet->ioc instanceof IPMVC_Model_IoCContainer);
	}
}

IPMVC::loadClass('AbstractPortlet');
class IPMVC_Model_PortletTest_Backing extends IPMVC_AbstractPortlet {
	public $property1 = null;
	public $ioc = null;
	public function setProperty1($var) {
		$this->property1 = $var;
	}
	public function setContext($context) {
		$this->ioc = $context;
	}
	public function process(IPMVC_Portlet_Request $request, IPMVC_Portlet_Response $response) {
	}
	public function render(IPMVC_Portlet_Request $request, IPMVC_Portlet_Response $response) {
	}
} 