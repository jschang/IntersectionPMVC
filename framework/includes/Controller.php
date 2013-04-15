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

IPMVC::loadClass('IPMVC_RequestProcessor');

abstract class IPMVC_Controller implements IPMVC_RequestProcessor {
	protected $iocContainer = null;
	protected $resourceSelector = null;
	
	/**
	 * Defines what properties may be set via the admin interface
	 */
	protected $properties = array();
	
	public function setProperties($properties) {
		$this->properties = $properties;
	}
	public function getProperties() {
		return $this->properties;
	}
	
	public function setIoCContainer(IPMVC_Model_IoCContainer $ioc) {
		$this->iocContainer = $ioc; 
	}
	public function getIoCContainer() {
		return $this->iocContainer;
	}
	
	public function setResourceSelector(IPMVC_Resource_Selector $selector) {
		$this->resourceSelector = $selector;
	}	
	public function getResourceSelector() {
		return $this->resourceSelector;
	}
	public function forward($controllerName,IPMVC_Request $request=null,IPMVC_Response $response=null) {
	    return IPMVC::runController($this->iocContainer->getObject($controllerName),$request,$response);
	}
	public function run(IPMVC_Request $request, IPMVC_Response $response) {
	    $this->request = $request;
	    $this->response = $response;
	    return $this->process($request,$response);
	}
	abstract public function process(IPMVC_Request $request, IPMVC_Response $response);
}
