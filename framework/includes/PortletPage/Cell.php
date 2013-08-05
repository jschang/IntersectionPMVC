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

IPMVC::loadClass('IPMVC_PortletPage_Component');

class IPMVC_PortletPage_Cell extends IPMVC_PortletPage_Component {
    private $portlet = null;
    private $ioc = null;
    private $portletUri = null;
	public function setIoC($ioc) {
		$this->ioc = $ioc;
	}
	public function setPortletUri($uri) {
		$this->portletUri = $uri;
	}
	public function setPortlet(IPMVC_Portlet $portlet) {
	    $this->portlet = $portlet;
	}
	public function getPortlet() {
	    return $this->portlet;
	}
	public function process(IPMVC_Portlet_Request $request) {
	    if(empty($this->portlet)) {
	        $this->portlet = $this->ioc->getObject("resource-selector")->getResource($this->portletUri);
	    }
		$this->portlet->process($request);
	}
	
	public function renderChild(IPMVC_Portlet_Request $request, IPMVC_Response $response) {
		$response->write("<div class=\"cell-".$this->getWidth()." ".$this->renderClasses()."\">\n");
		$portletResponse = new IPMVC_Portlet_Response();
		$this->portlet->render($request,$portletResponse);
		$response->write($portletResponse->getContent());
		$response->write("</div>\n");
	}
}