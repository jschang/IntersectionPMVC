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
JVS::loadClass('Model_IoCContainer');

class Model_IoCContainerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		JVS::loadClass('Model_IoCContainer');
		JVS::loadClass('Resource_File');
		$this->ioc = new Model_IoCContainer( new Resource_File((string)"IoCContainerTest.xml") );
	public function testProperties() {
		print_r($this);
		$tac = $this->ioc->getObject('testSetProperties');
	}
	public function testDoesNotExist() {
		$ioc = $this->ioc;
		$exceptionThrown = false;
		try {
			$ioc->getObject('DNE');
		} catch(Exception $e) {
			$exceptionThrown = true;
		}
		$this->assertTrue($exceptionThrown,"When requesting an object that does not exist, an exception should have been thrown.");
	}
	public function testSingleton() {
		$sing = $this->ioc->getObject('singleton');
		$this->assertEquals($sing,$this->ioc->getObject('singleton'),"An object defined with a singleton scope should be returned only once.");
		$sing = $this->ioc->getObject('factorySingleton');
		$this->assertEquals($sing,$this->ioc->getObject('factorySingleton'),"An object defined with a singleton scope should be returned only once.");
	}
	public function testArrayParams() {
		$tac = $this->ioc->getObject('testArrayConstructor');
		$ar = $tac->getProperty1();
		$this->assertTrue(
			is_array($ar)
			&& $ar['test'] instanceof Model_IoCContainerTest_TestObject
			&& $ar['test2'] == 'none'
			&& $ar[0] === $this->ioc->getObject('singleton')
		);
		$ar = $tac->getProperty2();
		$this->assertTrue($ar=='someValue');
	}
	public function testPrototyping() {
		$tac = $this->ioc->getObject('testArrayConstructor');
		$tac2 = $this->ioc->getObject('testArrayConstructor');
		$this->assertTrue( $tac!==$tac2 );
	}
}

class Model_IoCContainerTest_TestObject {
	public function __construct($arg1=null,$arg2=null) {
		$this->arg1 = $arg1;
		$this->arg2 = $arg2;
	}
	static public function staticFactoryMethod($arg1=null,$arg2=null) {
		return new Model_IoCContainerTest_TestObject($arg1,$arg2); 
	}
	public function factoryMethod($arg1=null,$arg2=null) {
		return new Model_IoCContainerTest_TestObject($arg1,$arg2);
	}
	public function setProperty1($arg1) {
		$this->arg1 = $arg1;
	}
	public function getProperty1() {
		return $this->arg1;
	}
	public function setProperty2($arg2) {
		$this->arg2 = $arg2;
	}
	public function getProperty2() {
		return $this->arg2;
	}
}