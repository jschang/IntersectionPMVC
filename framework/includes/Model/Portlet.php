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

JVS::loadClass('Resource');
JVS::loadClass('Resource_Selector');
JVS::loadClass('Model_IoCContainer');
JVS::loadClass('Exception_NotFound');
JVS::loadClass('Model_NodeUnmarshaller_Portlet');

class Model_Portlet {
	private $ioc = null;
	private $resourceSelector = null;
	
	public function setIoCContainer(Model_IoCContainer $ioc) {
		$this->ioc = $ioc; 
	}
	public function getIoCContainer() {
		return $this->ioc;
	}
	
	public function setResourceSelector(Resource_Selector $selector) {
		$this->resourceSelector = $selector;
	}	
	public function getResourceSelector() {
		return $this->resourceSelector;
	}
	
	public function getPortlet($uri) {
	
		$nodeParser = new Model_NodeUnmarshaller_Portlet();
		$nodeParser->setIoCContainer($this->ioc);
	
		$xmlSource = $this->resourceSelector->getResource($uri)->getContent();
		$portletXml = new DOMDocument();
		$portletXml->loadXml($xmlSource);
		$portletXml->lookupNamespaceUri($nodeParser->getNamespace());
		
		$xpath = new DOMXPath($portletXml);
		$xpath->registerNamespace('r',$nodeParser->getNamespace());
		
		$portletXpath = "//r:portlet";
		$portletNodes = $xpath->query($portletXpath);
		if( $portletNodes->length != 1 ) {
			throw new Exception("Expecting a single portlet node in the resource ".$uri);
		}
				
		$portlet = $nodeParser->parseNode($portletNodes->item(0));
		
		if( ! $portlet instanceof Portlet ) {
			throw new Exception_InvalidType(0,"Portlet",get_class($portlet));
		}
		
		return $portlet;
	}
}
