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

IPMVC::loadClass('IPMVC_View');

define('GRID_CLASS_CELL_START','ipmvc_st');
define('GRID_CLASS_CELL_END','ipmvc_nd');
define('GRID_CLASS_ROW','ipmvc_row');
define('GRID_CLASS_COLUMN','ipmvc_col');

class IPMVC_PortletPage_Component implements IPMVC_View {

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
	
	public function setParent(IPMVC_PortletPage_Component $parent) {
		$this->parentComponent = $parent;
	}
	public function getParent() {
		return $this->parentComponent;
	}
	
	/**
	 * @param PortletPage_Component
	 * @return If the replacement was made the child index, else null.
	 */
	public function replaceChild(IPMVC_PortletPage_Component $childObject,IPMVC_PortletPage_Component $replacement) {
		$childIndex = null;
		foreach( $this->children as $idx=>$child ) {
			if( $child==$childObject ) {
				$childIndex = $idx;
				break;
			}
		}
		if( $childIndex!==null ) {
			$this->children[$childIndex]=$replacement;
		}
		return $childIndex;
	}
	public function addChild(IPMVC_PortletPage_Component $childObject) {
		$this->children[]=$childObject;
		return $childObject;
	}
	public function getChildren() {
		return $this->children;
	}

	public function setId($id) {
		$this->id = $id;
	}
	public function getId() {
		return $this->id;
	}
	/**
	 * Replace the descendent node with the specified id.
	 *
	 * @param String the id of the component to replace
	 * @param PortletPage_Component the component to replace with
	 * @return PortletPage_Component the component replaced
	 */
	public function replaceId($id,IPMVC_PortletPage_Component $node) {
		$found = $this->findId($id);
		if( $found!=null && $found['parent']!=null ) {
			return $found['parent']->replaceChild($found['node'],$node);
		}
		return null;
	}
	/**
	 * @param String the id of the component to find
	 * @return array An array with the 'parent' and the 'node'.  
	 *   'parent' is null if the node found is the root node.  
	 *   null is returned if the node is not found.
	 */
	public function findId($id,IPMVC_PortletPage_Component $parent=null) {
		if( $this->getId()==$id ) {
			return array('parent'=>$parent,'node'=>$this);
		}
		foreach( $this->children as $child ) {
			$ret = $child->findId($id,$this);
			if( $ret!=null ) {
				return $ret;
			}
		}
		return null;
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
	
	public function process(IPMVC_Portlet_Request $portletRequest) {
		
		$children = $this->getChildren();
		foreach( $children as $child ) {
			$child->process($portletRequest);
		}
	}

	public function render(IPMVC_Request $request, IPMVC_Response $response) {
		
		$portletRequest = new IPMVC_Portlet_Request();
		$portletRequest->setHttpRequest($request);
		
		$this->renderChild($portletRequest,$response);
	}
	
	public function renderChild(IPMVC_Portlet_Request $request, IPMVC_Response $response) {
		$response->write("<div ");
		if(!empty($this->id)) {
			$response->write("id=\"".$this->id."\" ");
		}
		$response->write("class=\"".$this->renderClasses()."\">\n");
		$children = $this->getChildren();
		foreach( $children as $child ) {
			$child->renderChild($request,$response);
		}
		$response->write("</div>\n");
	}
}