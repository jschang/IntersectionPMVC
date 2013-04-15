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
IPMVC::loadClass('IPMVC_Resource_Selector');
IPMVC::loadClass('IPMVC_Resource_Selector_File');

class Resource_SelectorTest extends PHPUnit_Framework_TestCase {
	public function testFileSelector() {
		$selector = new IPMVC_Resource_Selector();
		$selector->setProtocolHandlers(array(
			'file'=>new IPMVC_Resource_Selector_File()
		));
		$file = $selector->getResource('file://./SelectorTest.php');
		$this->assertTrue(strlen($file->getContent())>100);
	}
}