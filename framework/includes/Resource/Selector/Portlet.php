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

JVS::loadClass('Model_Portlet');

class Resource_Selector_Portlet implements Resource_Selector_Interface {

	private $portletModel = null;
	private $selector = null;

	public function getProtocols() {
		return array('portlet');
	}
	
	public function setPortletModel(Model_Portlet $portletModel) {
		$this->portletModel = $portletModel;
	}
	public function getPortletModel() {
		return $this->portletModel;
	}
	
	public function getResource($uri) {
		preg_match('/([^:]*):\/\/(.*)/',$uri,$match);
		switch($match[1])
		{
			case 'portlet':
				return $this->portletModel->getPortlet('site-root://portlets/'.$match[2]);
				break;
		}
		return null;
	}
}
