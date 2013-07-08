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
	public function setIoC($ioc) {
		$this->ioc = $ioc;
	}
	public function setPortletUri($uri) {
		$this->portletUri = $uri;
	}
	public function render(IPMVC_Request $request, IPMVC_Response $response) {
		$response->write("<div class=\"cell-".$this->getWidth()." ".$this->renderClasses()."\">\n");
		$this->renderCell($request, $response);
		$response->write("</div>\n");
	}
	public function renderCell(IPMVC_Request $request, IPMVC_Response $response) {
		$o = $this->ioc->getObject("resource-selector")->getResource($this->portletUri);
		$response->write("&nbsp;");
	}
}