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

IPMVC::loadClass('IPMVC_Resource_Content');

class IPMVC_Resource_File implements IPMVC_Resource_Content {
	public $uri = null;
	public function __construct($fullPath="") {
		$this->fullPath = $fullPath;
	}
	public function getURI() {
		return $this->uri;
	}
	public function setURI($uri) {
		$this->uri = $uri;
	}
	public function getContent() {
		return file_get_contents($this->fullPath);
	}
	public function setContent($content) {
	    throw new IPMVC_Exception_Unsupported("Set content is not currently supported");
	}
	public function getLastModified() {
	    return filemtime($this->fullPath);
	}
	public function getCreatedTime() {
	    return filectime($this->fullPath);
	}
}
