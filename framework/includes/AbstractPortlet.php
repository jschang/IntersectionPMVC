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

IPMVC::loadClass('IPMVC_Portlet');
IPMVC::loadClass('IPMVC_Portlet_Request');
IPMVC::loadClass('IPMVC_Portlet_Response');

abstract class IPMVC_AbstractPortlet implements IPMVC_Portlet {

	private $portletResourceUri = null;
	
	abstract public function process(IPMVC_Portlet_Request $request, IPMVC_Portlet_Response $response);
	abstract public function render(IPMVC_Portlet_Request $request, IPMVC_Portlet_Response $response);
	
	public function getURI() {
		return $this->portletResourceUri;
	}
	public function setURI($uri) {
		$this->portletResourceUri = $uri;
	}
}