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

IPMVC::loadClass('IPMVC_Exception_Unsupported');

class IPMVC_Resource_Selector {
	private $protocolHandlers = array();
	public function setProtocolHandlers($protocolHandlers) {
		foreach($protocolHandlers as $handler) {
			$prots = $handler->getProtocols();
			foreach( $prots as $protocol )
				$this->protocolHandlers[$protocol][]=$handler;
		}			
	}
	public function getResource($uri) {
		preg_match('/([^:]*):\/\/(.*)/',$uri,$match);
		if( empty($this->protocolHandlers[$match[1]]) )
			throw new IPMVC_Exception_Unsupported(__CLASS__,$match[1]);
		else {
			foreach( $this->protocolHandlers[$match[1]] as $handler ) {
				$resource = $handler->getResource($uri);
				if( !empty($resource) )
					return $resource;
			}
		}
		return null;
	}
}
