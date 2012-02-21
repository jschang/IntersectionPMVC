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
JVS::loadClass('Model_IoCContainer');
JVS::loadClass('Model_NodeUnmarshaller_Routing');

class Model_Routing {
		
	private $ioc = null;
		
	public function setIoCContainer(Model_IoCContainer $ioc) {
		$this->ioc = $ioc;
	}
	public function getIoCContainer() {
		return $this->ioc;
	}
		
	public function __construct(Resource_Content $resource) {
		$this->xmlSource = $resource;
		$this->parser = new Model_NodeUnmarshaller_Routing();
	}
	
	public function getController($uri) {
		if( empty($this->pathing) )
			$this->parseSource();
			
		if( !empty($this->ioc) )
			$this->parser->setIoCContainer($this->ioc);

		foreach( $this->pathing as $regex=>$pathNode )
			if( preg_match('/'.$regex.'/',$uri) ) {
				$controller = $this->parser->parseNode($pathNode);
				if( !empty($this->controllerResourceSelector) && !$controller->getResourceSelector() )
					$controller->setResourceSelector($this->controllerResourceSelector);
				if( !empty($this->controllerIoCContainer) && !$controller->getIoCContainer() )
					$controller->setIoCContainer($this->controllerIoCContainer);
				return $controller;
			}
		return null;
	}
	
	private function parseSource() {
		$doc = new DOMDocument();
		$doc->loadXml($this->xmlSource->getContent());
		$xpath = new DOMXPath($doc);
		$xpath->registerNamespace('r',$this->parser->getNamespace());
		
		$routing = $xpath->query('//r:routing');
		$this->parseIocContainer( $routing->item(0)->getAttribute('ioc-container') );
		$this->parseResourceSelector( $routing->item(0)->getAttribute('resource-selector') );
		
		$paths = $xpath->query('//r:routing/r:path');
		foreach( $paths as $pathNode )
			$this->pathing[str_replace('/','\/',$pathNode->getAttribute('regex'))] = $pathNode;
	}
	
	private function parseIoCContainer($uri) {
		if( !empty($this->ioc) && ( $uri=="application" || empty($uri) ) )
			$this->controllerIoCContainer = $this->ioc;
		elseif( !empty($this->ioc) && ! empty($uri) )
			$this->controllerIoCContainer = new Model_IoCContainer( $this->ioc->getObject('resource-selector')->getResource($uri) );
	}
	
	private function parseResourceSelector($uri) {
		if( !empty($uri) ) {
			$parts = explode(':',$uri);
			if( $parts[0] == 'ioc' )
				$this->controllerResourceSelector = $this->controllerIoc->getObject($parts[1]);
			elseif( !empty($this->ioc) )
				$this->controllerResourceSelector = $this->ioc->getObject($parts[0]);
		}
	}
}