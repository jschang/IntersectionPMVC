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
			throw new IPMVC_Exception_NotFound($uri);
		}
IPMVC::log('found: '.$uri);
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
		$basePage = null;
		$basePageUri = $rootNode->getAttribute('base-page-uri');
		if( !empty($basePageUri) ) {
IPMVC::log('processing base-page-uri: '.$basePageUri);
			$basePage = $this->resourceSelector->getResource($basePageUri);
IPMVC::log('base page object: '.is_object($basePage)?get_class($basePage):'null');
		}
		$portlet = $nodeParser->parseNode($rootNode,null,null,$basePage);
		
		// merge select attributes of the portlet onto the basePage
		// ultimately, the basePage is what we're building and will be
		// passed back...the more top layers are only for overrides.
		$this->merge($basePage,$portlet);
		
IPMVC::log("stylesheets count: ".count($portlet->getStylesheets()));		
IPMVC::log("portlet page resulting: ".(is_object($basePage)?get_class($basePage):'null'));
		if( ! $portlet instanceof IPMVC_PortletPage ) {
			throw new IPMVC_Exception_InvalidClass("IPMVC_PortletPage",$portlet);
		}

		return !empty($basePage)?$basePage:$portlet;
	}
	
	/**
	 * Merge select elements from the portletPage onto the basePage
	 */
	private function merge($basePage,$portletPage) {
	    if(empty($basePage)) {
	        return;
	    }
	    foreach($portletPage->getStylesheets() as $link) {
IPMVC::log("adding stylesheet: ".$link);
	        $basePage->addStylesheet($link);
	    }
	}
}