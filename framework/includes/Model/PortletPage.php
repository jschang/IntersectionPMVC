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

IPMVC::loadClass('IPMVC_Resource_File');
IPMVC::loadClass('IPMVC_Exception_InvalidClass');
IPMVC::loadClass('IPMVC_Model_NodeUnmarshaller_PortletPage');

class IPMVC_Model_PortletPage {
	private $iocContainer = null;
	private $resourceSelector = null;
	private $nodeParser = null;
	
	public function setNodeParser(IPMVC_Model_NodeUnmarshaller_PortletPage $nodeParser) {
		$this->nodeParser = $nodeParser;
	}
	
	public function setIoCContainer(IPMVC_Model_IoCContainer $ioc) {
		$this->iocContainer = $ioc; 
	}
	
	public function setResourceSelector(IPMVC_Resource_Selector $selector) {
		$this->resourceSelector = $selector;
	}	
	
	public function getPortletPage($uri) {
	
		if( $this->nodeParser==null ) {
			$nodeParser = new IPMVC_Model_NodeUnmarshaller_PortletPage();
		} else {
			$nodeParser = $this->nodeParser;
		}
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
		
		$rootNode = $portletPageNodes->item(0);
		
		// see if this page definition is extending another
		$basePageUri = $rootNode->getAttribute("base-page-uri");
		$basePage = null;
		if( !empty($basePageUri) ) {
			$basePage = $this->resourceSelector->getResource($basePageUri);
		}
		
		$portlet = $nodeParser->parseNode($rootNode,null,null,$basePage);
		
		if( ! $portlet instanceof IPMVC_PortletPage ) {
			throw new IPMVC_Exception_InvalidClass("IPMVC_PortletPage",$portlet);
		}
		
		return !empty($basePage)?$basePage:$portlet;
	}
}