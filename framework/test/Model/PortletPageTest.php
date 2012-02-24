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
JVS::loadClass('Model_PortletPage');

class Model_PortletPageTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		JVS::loadClass('Model_IoCContainer');
		JVS::loadClass('Resource_Selector');
		JVS::loadClass('Resource_Selector_File');
		JVS::loadClass('Resource_File');
		
		$this->portletPageModel = new Model_PortletPage();
		
		$this->selector = new Resource_Selector();
		$this->selector->setProtocolHandlers(array(
			'file'=>new Resource_Selector_File()
		));
		
		$this->ioc = new Model_IoCContainer( $this->selector->getResource("file://./PortletPageTest_IoC.xml") );
		
		$this->portletPageModel->setResourceSelector($this->selector);
		$this->portletPageModel->setIoCContainer($this->ioc);
	}
	public function testLoadPage() {
		$this->portletPageModel->getPortletPage("file://./PortletPageTest.xml");
		$this->assertTrue(true);
	}
}