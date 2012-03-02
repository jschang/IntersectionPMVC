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

JVS::loadClass('PortletPage');
JVS::loadClass('PortletPage_Component');
JVS::loadClass('PortletPage_Link');

class PortletPage_Generic extends PortletPage_Component implements PortletPage {
	private $uri = null;
	private $styleSheets = array();
	
	public function __construct() {
		$link = new PortletPage_Link();
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
	
	public function process(Request $request, Response $response) {
		$response->write("<html><head>");
		foreach( $this->styleSheets as $styleSheet ) {
			$styleSheet->process($request,$response);
		}
		$response->write("</head><body>");
		$children = $this->getChildren();
		foreach( $children as $child ) {
			$child->process($request,$response);
		}
		$response->write("</body></html>");
	}
}