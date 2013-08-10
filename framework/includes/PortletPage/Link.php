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

class IPMVC_PortletPage_Link extends IPMVC_PortletPage_Component {
	
	private $uri = null;
	private $rel = "stylesheet";
	private $type = "text/css";
	private $mediaType = "all";
	
	public function __construct($uri=null,$mediaType="all",$rel="stylesheet",$type="text/css") {
		$this->setUri($uri);
		$this->setMediaType($mediaType);
		$this->setType($type);
		$this->setRel($rel);
	}
	
	public function setUri($uri) {
		$this->uri = $uri;
	}
	public function getUri() {
		return $this->uri;
	}
	
	public function setRel($rel) {
		$this->rel = $rel;
	}
	public function getRel() {
		return $this->rel;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	public function getType() {
		return $this->type;
	}
	
	public function setMediaType($mediaType) {
		$this->mediaType = $mediaType;
	}
	public function getMediaType() {
		return $this->mediaType;
	}

	public function renderChild(IPMVC_Portlet_Request $request, IPMVC_Response $response) {
		$response->write($this."\n");
	}
	
	public function __toString() {
	    return "<link rel=\"".$this->getRel()."\" "
			."href=\"".$this->getUri()."\" "
			."type=\"".$this->getType()."\" "
			."media=\"".$this->getMediaType()."\" />";
	}
}