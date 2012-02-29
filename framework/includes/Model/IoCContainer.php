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

class Model_IoCContainer {
	private $containerSource = null;
	private $objects = array();
	private $parser = null;
	
	public function __construct(Resource_Content $resource) {
		
		JVS::loadClass('Model_NodeUnmarshaller_IoCContainer');
		
		$this->parser = new Model_NodeUnmarshaller_IoCContainer();
		$this->parser->setIoCContainer($this);
		
		$this->merge($resource);
	}
	
	public function merge(Resource $resource) {
		if( empty($resource) )
			return; 
		$this->containerSource = $resource;
		$this->parseSource();
	}
		
	/**
	 * Creates an inventory of objects
	 */
	private function parseSource() {
		$dom = new DOMDocument();
		$dom->loadXML($this->containerSource->getContent(),LIBXML_NOBLANKS);
		$xpath = new DOMXPath($dom);
		$xpath->registerNamespace('ioc',$this->parser->getNamespace());
		$objects = $xpath->query('//ioc:objects/ioc:object');
		foreach($objects as $obj) {
			$this->objects[$obj->getAttribute('id')]=$obj;
		}
	}
	
	public function isSingleton($id) {
		return ( 
			! $this->objects[$id] instanceof DOMElement 
			|| ( $this->objects[$id] instanceof DOMElement && $this->objects[$id]->getAttribute('scope')=='singleton' ) 
		);
	}
	
	public function singletonInstantiated($id) {
		return ( $this->isSingleton($id) && ! $this->objects[$id] instanceof DOMElement );
	}
	
	public function getObject($id) {
		
		if( empty($this->objects[$id]) )
			throw new RuntimeException("There is no object ".$id." in the IoC");
			
		if( $this->objects[$id] instanceof DOMElement ) {
			$object = $this->parser->parseNode($this->objects[$id]);
			if( $this->objects[$id] instanceof DOMElement && $this->objects[$id]->getAttribute('scope')=='singleton' ) {
				$this->objects[$id]=$object;
			}
			return $object;
		} else return $this->objects[$id];
	}
	
	public function setObject($id,$object) {
		$this->objects[$id]=$object;
	}
}