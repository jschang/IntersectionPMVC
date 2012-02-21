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

class Resource_Selector_PortletPage implements Resource_Selector_Interface {
	private $pageModel = null;
	public function getProtocols() {
		return array('portlet-page');
	}
	public function setPortletPageModel(Model_PortletPage $pageModel) {
		$this->pageModel = $pageModel;
	}
	public function getPortletPageModel() {
		return $this->pageModel;
	}
	public function getResource($uri) {
		preg_match('/([^:]*):\/\/(.*)/',$uri,$match);
		switch($match[1])
		{
			case 'portlet-page':
				//$this->pageModel->
				die('here in Resource_Selector_PortletPage for page '.$uri);
				break;
		}
		return null;
	}
}
