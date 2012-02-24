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

JVS::loadClass('Exception_InvalidClass');
JVS::loadClass('Model_NodeUnmarshaller');

class Model_NodeUnmarshaller_PortletPage implements Model_NodeUnmarshaller {
	private $xmlNs = 'urn:Model_NodeUnmarshaller_PortletPage';
	private $ioc = null;
	private $nodeClassMap = array(
			'portlet-page'=>'PortletPage_Generic',
			'row'=>'PortletPage_Row',
			'cell'=>'PortletPage_Cell',
			'column'=>'PortletPage_Column',
			'portlet'=>'PortletPage_Portlet'
		);
	
	public function getNamespace() {
		return $this->xmlNs;
	}
	public function setNamespace($ns) {
		$this->xmlNs = $ns;
	}
	
	public function setIoCContainer(Model_IoCContainer $ioc) {
		$this->ioc = $ioc;	
	}
	public function getIoCContainer() {
		return $this->ioc;
	}
	
	public function parseNode(DOMNode $node, $nodeName=null) {
	
		if( empty($nodeName) ) {
			$nodeName = $node->nodeName;
		}
		
		$nodeClassMap = $this->nodeClassMap;
		if( empty($nodeClassMap[$nodeName]) ) {
			throw new Exception("$nodeName is not a valid node");
		}
		
		$ref = $node->getAttribute('ref');
		if( !empty($ref) && !empty($this->ioc) ) {
			return $this->iocParser->parseObject($node,$this->ioc->getObject($ref));
		}
		
		$backing = $node->getAttribute('backing');
		if( !empty($backing) ) {
			$node->setAttribute('class',$backing);
		} else {
			$node->setAttribute('class',$nodeClassMap[$nodeName]);
		}
		
		$iocParser = new Model_NodeUnmarshaller_IoCContainer();
		if( !empty($this->ioc) ) {
			$iocParser->setIoCContainer($this->ioc);
		}
		$iocParser->setNamespace($this->xmlNs);
		$obj = $iocParser->parseNode($node,'object');
		
		if( ! is_a($obj, $nodeClassMap[$nodeName]) ) {
			throw new Exception_InvalidClass("PortletPage_Cell",$obj);
		}
		foreach( $node->childNodes as $childNode ) {
			if($childNode->nodeName=='portlet-page') {
				throw new Exception("portlet-page may only be the root node");
			}
			$childObj = $this->parseNode($childNode);
			print_r($childObj);
		}
		
		return $obj;
	}
}