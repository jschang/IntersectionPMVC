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

IPMVC::loadClass('IPMVC_Model_Portlet');

class IPMVC_Resource_Selector_Portlet implements IPMVC_Resource_Selector_Interface {

	private $portletModel = null;
	private $selector = null;
	private $portletsRootPath = 'site-root://portlets';
	
	public function getProtocols() {
		return array('portlet');
	}
	
	public function setPortletModel(IPMVC_Model_Portlet $portletModel) {
		$this->portletModel = $portletModel;
	}
	public function getPortletModel() {
		return $this->portletModel;
	}
	
	public function setPortletsRootPath($path) {
		$this->portletRootPath = $path;
	}
	public function getPortletsRootPath() {
		return $this->portletsRootPath;
	}
	
	public function getResource($uri) {
		preg_match('/([^:]*):\/\/(.*)/',$uri,$match);
		switch($match[1])
		{
			case 'portlet':
				return $this->portletModel->getPortlet($this->portletsRootPath.'/'.$match[2]);
				break;
		}
		return null;
	}
	public function getReturnClass() {
	    return 'IPMVC_Portlet';
	}
}
