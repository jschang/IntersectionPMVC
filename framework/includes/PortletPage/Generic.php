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
	
	public function getURI() {
		return $this->uri;
	}
	public function setURI($uri) {
		$this->uri = $uri;
	}
	
	public function setStylesheets($styleSheets) {
	    $newStylesheets = array();
	    foreach($styleSheets as $link) {
	        $newStylesheets[''.$link]=$link;
	    }
		$this->styleSheets=$newStylesheets;
	}
	public function getStylesheets() {
		return $this->styleSheets;
	}
	public function addStylesheet(IPMVC_PortletPage_Link $link) {
	    $this->styleSheets[''.$link]=$link;
	}
	
	public function renderChild(IPMVC_Portlet_Request $request, IPMVC_Response $response) {
		$response->write("<html><head>");
		$requestUriParts = explode('?',$_SERVER['REQUEST_URI']);
		$response->write('<base href="/" target="_blank">');
		foreach( $this->styleSheets as $styleSheet ) {
			$styleSheet->renderChild($request,$response);
		}
		$response->write("</head>");
		$response->write("<body>");
		$response->write("<div class=\"ipmvc_grid\">");
		$children = $this->getChildren();
		foreach( $children as $child ) {
			$child->renderChild($request,$response);
		}
		$response->write("</div></body></html>");
	}
}