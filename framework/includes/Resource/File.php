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

JVS::loadClass('Resource_Content');

class Resource_File implements Resource_Content {
	public $uri = null;
	public function __construct($fullPath="") {
		$this->uri = $fullPath;
	}
	public function getURI() {
		return $this->uri;
	}
	public function setURI($uri) {
		$this->uri = $uri;
	}
	public function getContent() {
		return file_get_contents($this->uri);
	}
	public function setContent($content) {
	}
}
