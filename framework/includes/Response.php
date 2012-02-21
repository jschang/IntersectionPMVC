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

class Response {
	private $body = "";
	private $contentType = "text/html";
	
	public function setContentType($contentType) {
		$this->contentType = $contentType;
	}
	public function getContentType() {
		return $this->contentType;
	}
	
	public function setBody($content) {
		$this->body = $content;
	}
	public function getBody() {
		return $this->body;
	}
	
	public function sendHeaders() {
		header('content-type: '.$this->contentType);
	}
	
	public function sendAndExit() {
		$this->sendHeaders();
		echo $this->body;
		exit();
	}
}
