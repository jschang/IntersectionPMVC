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

IPMVC::loadClass('IPMVC_Model_NodeUnmarshaller_IoCContainer');
IPMVC::loadClass('IPMVC_Model_NodeUnmarshaller');

class IPMVC_Model_NodeUnmarshaller_Routing implements IPMVC_Model_NodeUnmarshaller {

	private $ioc = null;
	private $iocParser = null;
	private $xmlNs = 'urn:IPMVC_Model_NodeUnmarshaller_Routing';
		
	public function __construct() {
		$this->iocParser = new IPMVC_Model_NodeUnmarshaller_IoCContainer();
		$this->iocParser->setNamespace($this->getNamespace());
	}
		
	public function setIoCContainer(IPMVC_Model_IoCContainer $ioc) {
		$this->ioc = $ioc;	
	}
	public function getIoCContainer() {
		return $this->ioc;
	}
	
	public function getNamespace() {
		return $this->xmlNs;
	}
	public function setNamespace($ns) {
		$this->xmlNs = $ns;
	}
	
	public function parseNode(DOMNode $node,$nodeName=null) {
		if( empty($nodeName) )
			$nodeName = $node->nodeName;
		if( !empty($this->ioc) )
			$this->iocParser->setIoCContainer($this->ioc);
		switch($nodeName) {
			case 'path': return $this->parsePath($node); break;
		}
	}
	
	public function parsePath(DOMNode $node) {
	
		$ref = $node->getAttribute('ref');
		if( !empty($ref) && !empty($this->ioc) ) {
			return $this->iocParser->parseObject($node,$this->ioc->getObject($ref));
		}
		
		$backing = $node->getAttribute('backing');
		if( !empty($backing) ) {
			$node->setAttribute('class',$backing);
		}
		
		return $this->iocParser->parseObject($node);
	}
}
