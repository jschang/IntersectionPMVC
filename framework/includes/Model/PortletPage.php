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

JVS::loadClass('Resource_File');
JVS::loadClass('Exception_InvalidClass');
JVS::loadClass('Model_NodeUnmarshaller_PortletPage');

class Model_PortletPage {
	private $iocContainer = null;
	private $resourceSelector = null;
	
	public function setIoCContainer(Model_IoCContainer $ioc) {
		$this->iocContainer = $ioc; 
	}
	public function getIoCContainer() {
		return $this->iocContainer;
	}
	
	public function setResourceSelector(Resource_Selector $selector) {
		$this->resourceSelector = $selector;
	}	
	public function getResourceSelector() {
		return $this->resourceSelector;
	}
	
	public function getPortletPage($uri) {
		$nodeParser = new Model_NodeUnmarshaller_PortletPage();
		$nodeParser->setIoCContainer($this->iocContainer);
	
		$res = $this->resourceSelector->getResource($uri);
		if( $res==null ) {
			throw new Exception_NotFound($uri);
		}
		$xmlSource = $res->getContent();
		$pageXml = new DOMDocument();
		$pageXml->loadXml($xmlSource,LIBXML_NOBLANKS);
		$pageXml->lookupNamespaceUri($nodeParser->getNamespace());
		
		$xpath = new DOMXPath($pageXml);
		$xpath->registerNamespace('r',$nodeParser->getNamespace());
		
		$pageXpath = "//r:portlet-page";
		$portletPageNodes = $xpath->query($pageXpath);
		if( $portletPageNodes->length != 1 ) {
			throw new Exception("Expecting a single portlet node in the resource ".$uri);
		}
				
		$portlet = $nodeParser->parseNode($portletPageNodes->item(0));
		
		if( ! $portlet instanceof PortletPage ) {
			throw new Exception_InvalidClass("PortletPage",$portlet);
		}
		
		return $portlet;
	}
}