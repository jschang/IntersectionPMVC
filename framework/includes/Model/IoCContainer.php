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

class IPMVC_Model_IoCContainer {
	private $containerSource = null;
	private $objects = array();
	private $parser = null;
	private $preprocessors = array();
	private $parsingPreProcessors = false;
	
	public function __construct(IPMVC_Resource_Content $resource) {
		
		IPMVC::loadClass('IPMVC_Model_NodeUnmarshaller_IoCContainer');
		
		$this->parser = new IPMVC_Model_NodeUnmarshaller_IoCContainer();
		$this->parser->setIoCContainer($this);
		
		$this->merge($resource);
	}
	
	public function merge(IPMVC_Resource $resource) {
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
		$this->preprocessors = array();
		foreach($objects as $obj) {
			$this->objects[$obj->getAttribute('id')]=$obj;
			if( is_subclass_of($obj->getAttribute('class'),'IPMVC_IoC_ParamValuePreProcessor') ) {
				$this->preprocessors[$obj->getAttribute('id')]=$obj;
			}
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
		
		if(!empty($this->preprocessors)
			&& reset($this->preprocessors) instanceof DOMElement
			&& !$this->parsingPreProcessors // to prevent infinite recursion
			) {
			$this->parsingPreProcessors = true;
			foreach($this->preprocessors as $i=>$o) {
				$this->objects[$i]
					= $this->preprocessors[$i]
					= $this->parser->parseNode($o);
			}
			$this->parsingPreProcessors = false;
		}
		
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
	
	public function preProcessValue($value) {
		if($this->parsingPreProcessors)
			return $value;
		$ret = $value;
		foreach($this->preprocessors as $processor) {
			$ret = $processor->process($value);			
		}
		return $ret;
	}
	
	public function __get($id) {
	    return $this->getObject($id);
	}
	
	public function __isset($id) {
	    return !empty($this->objects[$id]);
	}
}