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

JVS::loadClass('RequestProcessor');

define('GRID_CLASS_CELL_START','st');
define('GRID_CLASS_CELL_END','nd');
define('GRID_CLASS_ROW','row');
define('GRID_CLASS_COLUMN','col');
define('GRID_PATH_CSS','/css/grid.css');

class PortletPage_Component implements RequestProcessor {

	private $id = null;
	private $classes = array();
	private $parentComponent = null;
	private $children = array();
	private $width = null;
	
	public function setWidth($width) {
		$this->width = $width;
	}
	public function getWidth() {
		return $this->width;
	}
	
	public function setParent(PortletPage_Component $parent) {
		$this->parentComponent = $parent;
	}
	public function getParent() {
		return $this->parentComponent;
	}
	
	public function addChild($childObject) {
		$this->children[]=$childObject;
		return $childObject;
	}
	public function getChildren() {
		return $this->children;
	}

	public function setId($id) {
		$this->id = $id;
	}
	public function getId($id) {
		return $this->id;
	}
	
	public function setClasses(array $classes) {
		$this->classes = $classes;
	}
	public function getClasses() {
		return $this->classes;
	}
	public function addClass($class) {
		$this->classes[$class]=true;
	}
	public function removeClass($class) {
		if( isset($this->classes[$class]) ) {
			unset($this->classes[$class]);
		}
	}
	public function renderClasses() {
		$keys = array_keys($this->classes);
		return implode(' ',$keys);
	}
	
	public function process(Request $request, Response $response) {
		$response->write("<div class=\"".$this->renderClasses()."\">\n");
		$children = $this->getChildren();
		foreach( $children as $child ) {
			$child->process($request,$response);
		}
		$response->write("</div>\n");
	}
}