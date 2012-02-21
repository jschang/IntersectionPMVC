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

abstract class Portlet implements Resource {
	private $ioc = null;
	private $resourceSelector = null;
	private $portletResourceUri = null;
	
	/**
	 * Defines what properties may be set via the admin interface
	 */
	private $properties = array();
	
	abstract public function process(Portlet_Request $request, Portlet_Response $response);
	abstract public function render(Portlet_Request $request, Portlet_Response $response);
	
	public function setProperties($properties) {
		$this->properties = $properties;
	}
	public function getProperties() {
		return $this->properties;
	}
	
	public function setIoC(Model_IoCContainer $ioc) {
		$this->ioc = $ioc; 
	}
	public function getIoC() {
		return $this->ioc;
	}
	
	public function setResourceSelector($selector) {
		$this->resourceSelector = $selector;
	}	
	public function getResourceSelector() {
		return $this->resourceSelector;
	}
	
	public function getURI() {
		return $this->portletResourceUri;
	}
	public function setURI($uri) {
		$this->portletResourceUri = $uri;
	}
}