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

IPMVC::loadClass('PortletPage');
IPMVC::loadClass('PortletPage_Component');
IPMVC::loadClass('PortletPage_Link');

class IPMVC_PortletPage_Generic extends IPMVC_PortletPage_Component implements IPMVC_PortletPage {
	private $uri = null;
	private $styleSheets = array();
	
	public function __construct() {
		$link = new IPMVC_PortletPage_Link();
		$link->setUri(GRID_PATH_CSS);
		$this->styleSheets[]=$link;
	}
	
	public function getURI() {
		return $this->uri;
	}
	public function setURI($uri) {
		$this->uri = $uri;
	}
	
	public function setStylesheets($styleSheets) {
		$this->styleSheets=$styleSheets;
	}
	public function getStylesheets() {
		return $this->styleSheets;
	}
	
	public function render(IPMVC_Request $request, IPMVC_Response $response) {
		$response->write("<html><head>");
		foreach( $this->styleSheets as $styleSheet ) {
			$styleSheet->render($request,$response);
		}
		$response->write("</head>");
		$response->write("<body>");
		$response->write("<div class=\"grid\">");
		$children = $this->getChildren();
		foreach( $children as $child ) {
			$child->render($request,$response);
		}
		$response->write("</div></body></html>");
	}
}