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

IPMVC::loadClass('IPMVC_Model_NodeUnmarshaller');

/**
 * Pulled these methods out of the Model_IoCContainer because
 * I want to be able to the Xml schema in other Xml based models.
 */
class IPMVC_Model_NodeUnmarshaller_IoCContainer implements IPMVC_Model_NodeUnmarshaller {
	
	private $IoC = null;
	private $xmlNs = 'urn:IPMVC_Model_NodeUnmarshaller_IoCContainer';
	
	public function setIoCContainer(IPMVC_Model_IoCContainer $IoC) {
		$this->IoC = $IoC;
	}
	public function getIoCContainer() {
		return $this->IoC;
	}
	
	public function getNamespace() {
		return $this->xmlNs;
	}
	public function setNamespace($ns) {
		$this->xmlNs = $ns;
	}
	
	public function parseNode(DOMNode $node,$nodeName=null) {
		if( $nodeName==null ) {
			$nodeName = $node->nodeName;
		}
		switch( $nodeName )
		{
			case 'object':   return $this->parseObject($node); break;
			case 'param':    return $this->parseParam($node);  break;
			case 'property': return $this->parseParam($node);  break;
		}
	}
	
	/**
	 * @param DOMNode $object The context xml object definition
	 * @param Object $actualObject an existing object to merge the new object values onto
	 * @return object
	 */
	public function parseObject(DOMNode $object,$actualObject=null) {
		
		$xpath = new DOMXPath($object->ownerDocument);
		$xpath->registerNamespace('ioc',$this->xmlNs);
		
		if( empty($actualObject) )
		{
			// determine if we should be using a factory method to create the class
			$factoryRef = $object->getAttribute('factory-ref');
			$className = $object->getAttribute('class');
			$factoryObj = null;
			if( !empty($factoryRef) && !empty($this->IoC) ) {
				$factoryObj = $this->IoC->getObject($factoryRef);			
			} elseif( !empty($className) ) {
				$factoryObj = $className;
			}
		
			// determine the values for the constructor or factory method parameters
			$constructorParams = $xpath->query('./ioc:constructor-arguments/ioc:param',$object);
			$params = $this->parseParams($constructorParams);
			
			// actually construct the new object
			$factoryMethod = $object->getAttribute('factory-method');
			if( !empty($factoryMethod) ) {
				$newObject = call_user_func_array(array($factoryObj,$factoryMethod),$params);
			} else {
				$className = trim($className);
				if(empty($className)) {
					throw new Exception("Class name is empty for IoC object: ".print_r($object,true));
				}
			
				if( !class_exists($className) ) {
					IPMVC::loadClass($className);
				}
				$str = '$newObject = new '.$className.'(';
				$args=array();
				foreach($params as $idx=>$param) {
					$args[]='$params['.$idx.']';
				}
				$str.= implode(',',$args).');';
				eval($str);
			}
		} else { 
			$newObject = $actualObject; 
		}
		
		$id = $object->getAttribute('id');
		if( !empty($this->IoC) 
				&& !empty($id) 
				&& $this->IoC->isSingleton($id) 
				&& !$this->IoC->singletonInstantiated($id) ) {
			$this->IoC->setObject( $id, $newObject );
		}

		$properties = $xpath->query('./ioc:property',$object);
		$this->parseProperties($newObject,$properties);
			
		return $newObject;
		
	}
	
	/**
	 * @return array of method-name/value pairs
	 */
	public function parseProperties($newObject,DOMNodeList $properties) {
		foreach( $properties as $property ) {
			$value = $this->parseParam($property);
			$name = $property->getAttribute('name');
			$method = $property->getAttribute('method');
			if( !empty($method) )
				$newObject->$method($value);
			else {
				$method = 'set'.ucfirst($name);
				$newObject->$method($value);
			}
		}
	}
		
	/**
	 * @return array of values
	 */
	public function parseParams(DOMNodeList $params) {
		$values= array();
		foreach($params as $param) {
			$values[] = $this->parseParam($param);
		}
		return $values;
	}
	
	/**
	 * The core of each type of node: object, param, property.
	 *
	 * @return mixed either an object or scalar value
	 */
	public function parseParam(DOMNode $param) {
	   
		$ref = $param->getAttribute('ref');
		if( !empty($ref) && !empty($this->IoC) )
			return $ref=='this' ? $this->IoC : $this->IoC->getObject($ref);
			
		$class = $param->getAttribute('class');
		$factoryRef = $param->getAttribute('factory-ref');
		if( !empty($class) || !empty($factoryRef) ) {
			return $this->parseObject($param);
		}
			
		// scalar value always trumps anything contained
		$value = $param->getAttribute('value');
		if( !empty($value) ) {
			$interpret = $param->getAttribute('interpret');
			if(empty($interpret)) {
				$interpret = 'scalar';
			}
			
			switch($interpret) {
			case 'scalar':
				$value = $this->IoC->preProcessValue($value);
				return $value;
			case 'eval':
				eval('$value ='.$value.';');
				return $value;
			}
		}
		
		// process each element	
		$xpath = new DOMXPath($param->ownerDocument);
		$xpath->registerNamespace('ioc',$this->xmlNs);
		$arrayElements = $xpath->query('./ioc:value',$param);
		$callback = $param->getAttribute('callback');
		if( !empty($arrayElements) ) {
			$ar = $this->parseArrayElement($arrayElements);
			if( empty($callback) ) {
				return $ar;
			} else {
				return call_user_func($ar);
			}
		}		
	}
	
	public function parseArrayElement(DOMNodeList $arrayElements,$ar=array()) {
		foreach($arrayElements as $element) {
			$key = $element->getAttribute('key');
			$val = $this->parseParam($element);
			if( !empty($key) ) {
				$ar[$key] = $val;
			} else {
				$ar[] = $val;
			}
		}
		return $ar;
	}
}