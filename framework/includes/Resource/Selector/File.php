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

JVS::loadClass('Resource_Selector_Interface');
JVS::loadClass('Resource_File');

class Resource_Selector_File implements Resource_Selector_Interface {
	public function getProtocols() {
		return array('file','code-root','site-root');
	}
	public function getResource($uri) {
		$resource=null;
		preg_match('/([^:]*):\/\/(.*)/',$uri,$match);
		switch( $match[1] ) {
			case 'file':
				$file = $match[2];
				break;
			case 'code-root':
				$file = CODE_ROOT.DIRECTORY_SEPARATOR.$match[2];
				break;
			case 'site-root':
				$file = JVS::getSiteRoot().DIRECTORY_SEPARATOR.$match[2];
				break;
		}
		if( !empty($file) && file_exists($file) ) {
			$resource = new Resource_File($file);
			$resource->setURI($uri);
		}
		return $resource;
	}
}
