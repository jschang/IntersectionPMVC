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

IPMVC::loadClass('IPMVC_Exception_InvalidClass');
IPMVC::loadClass('IPMVC_Exception_InvalidType');
IPMVC::loadClass('IPMVC_Model_NodeUnmarshaller');

class IPMVC_Model_NodeUnmarshaller_PortletPage implements IPMVC_Model_NodeUnmarshaller {
	private $xmlNs = 'urn:IPMVC_Model_NodeUnmarshaller_PortletPage';
	private $ioc = null;
	private $nodePrototypeMap = array(
			'portlet-page'=>'portlet-page-prototype',
			'row'=>'portlet-page-row-prototype',
			'cell'=>'portlet-page-cell-prototype',
			'column'=>'portlet-page-column-prototype',
			'portlet'=>'portlet-page-portlet-prototype'
		);
	private $nodeClassMap = array(
			'portlet-page'=>'IPMVC_PortletPage_Generic',
			'row'=>'IPMVC_PortletPage_Row',
			'cell'=>'IPMVC_PortletPage_Cell',
			'column'=>'IPMVC_PortletPage_Column',
			'portlet'=>'IPMVC_PortletPage_Portlet'
		);
	private $portletPagePrototype = null;
	
	public function getNamespace() {
		return $this->xmlNs;
	}
	public function setNamespace($ns) {
		$this->xmlNs = $ns;
	}
	
	public function setIoCContainer(IPMVC_Model_IoCContainer $ioc) {
		$this->ioc = $ioc;	
	}
	
	public function setPortletPagePrototype($prototypeName) {
		$this->portletPagePrototype = $portletPagePrototype;
	}
	
	public function parseNode(
			DOMNode $node, 
			$nodeName=null, 
			IPMVC_PortletPage_Component $parentObject=null, 
			IPMVC_PortletPage $basePage=null 
		) {

//IPMVC::log($node->ownerDocument->saveXML($node));
	
		$iocParser = new IPMVC_Model_NodeUnmarshaller_IoCContainer();
		if( !empty($this->ioc) ) {
			$iocParser->setIoCContainer($this->ioc);
		}
		$iocParser->setNamespace($this->xmlNs);
	
		if( empty($nodeName) ) {
			$nodeName = $node->nodeName;
		}
		
		// in-case the dev wanted additional css classes on this element
		$cssClasses = $node->getAttribute('class');
		
		// construct the PortletPage-composite object
		$nodePrototypeMap =& $this->nodePrototypeMap;
		$nodeId = null;
		if( !empty($nodePrototypeMap[$nodeName]) ) {
			$ref = $node->getAttribute('ref');
			if( !empty($ref) && !empty($this->ioc) ) {
//IPMVC::log('returning parseObject');
				$obj = $this->ioc->getObject($ref);
			} else {
				
				$backing = $node->getAttribute('backing');
				if( !empty($backing) ) {
					$node->setAttribute('class',$backing);
				} else {
					// get the prototype from the IoC
					$ref = $nodePrototypeMap[$nodeName];
					$obj = $this->ioc->getObject($ref);
				}
				
				$nodeId = $node->getAttribute('id');
				if( !empty($nodeId) ) {
					$node->removeAttribute('id');
				}
				$obj->setId($nodeId);
			}
		}
		
		// parse any additional options to be passed into the node's object 
		$obj = $iocParser->parseObject($node,$obj);
		
		// look for children and recurse
		// setup additional
		if( !empty($nodePrototypeMap[$nodeName]) ) {
		    
//IPMVC::log("$nodeName found in \$nodePrototypeMap");

			if( ! is_a($obj, $this->nodeClassMap[$nodeName]) ) {
				throw new IPMVC_Exception_InvalidClass("PortletPage_Component",$obj);
			}
			
			$childCount = $node->childNodes->length;
			foreach( $node->childNodes as $idx=>$childNode ) {
			
			    // protect against bad structure
			    if($childNode->nodeName=='portlet-page') {
					throw new Exception("portlet-page may only be the root node");
				}
			    
			    // parse the stylesheet nodes
			    if($childNode->nodeName=='stylesheets'&&$childNode->childNodes->length) {
			        
			        $styleSheets = $obj->getStylesheets();
			        if(!$styleSheets) {
			            $styleSheets=array();
			        }
                    foreach($childNode->childNodes as $idx=>$styleSheetNode) {
                        $newSs = new IPMVC_PortletPage_Link();
                        foreach(array('rel'=>'setRel','href'=>'setUri','type'=>'setType','media-type'=>'setMediaType')
                                as $attr=>$setter) {
                            $value = $styleSheetNode->getAttribute($attr);
if($attr=='href') {
    IPMVC::log("found stylesheet: ".$value);
}
                            if(!empty($value)) {
                                $newSs->$setter($value);
                            }
                        }
                        $obj->addStylesheet($newSs);
                    }
			        // nothing more to be done at this level
			        continue;
			    }
				
//IPMVC::log('child node:'.$childNode->nodeName);	

                // tack on end and start classes for the beginning and end of a row
				if( !empty($nodePrototypeMap[$childNode->nodeName]) ) {
				
					$childObj = $this->parseNode($childNode,$childNode->nodeName,$obj,$basePage);
					if( $childObj instanceof IPMVC_PortletPage_Cell ) {
						if( $idx==0 ) {
							$childObj->addClass(GRID_CLASS_CELL_START);
						}
						if( $idx==($childCount-1) ) {
							$childObj->addClass(GRID_CLASS_CELL_END);
						}
					}
				}
			}
			
			if(!empty($cssClasses)) {
			    foreach(explode(' ',$cssClasses) as $class) {
			        $obj->addClass($class);
			    }
			}
			
			// parse those attributes which are essential for a cell
			if( $obj instanceof IPMVC_PortletPage_Cell ) {
                $width = $node->getAttribute("width");
                //if( empty($width) ) {
                //    throw new IPMVC_Exception_Configuration("Width is a required configuration for a PortletPage_Cell");
                //}
                if( !empty($width) ) {
                    if( !is_numeric($width) ) {
                        throw new IPMVC_Exception_InvalidType('Integer',$width);
                    }
                    $obj->setWidth(intval($width));
                }
                
                // if it is a page cell (visible block), then it may point to either a portlet.xml uri
                // or a site-root:// or vcr://, for simple-content
                $uri = $node->getAttribute("portlet-uri");
                if( empty($uri) && !$obj->getPortlet() ) {
                    throw new IPMVC_Exception_Configuration("portlet-uri is a required configuration attribute for a PortletPage_Cell");
                }
                if(!$obj->getPortlet()) {
                    $uriClasses = $this->ioc->getObject('resource-selector')->getReturnClasses($uri);
                    if(($classes = array_intersect(array('IPMVC_Resource_Content','IPMVC_Resource_File'),$uriClasses))
                            && !empty($classes)) {
                        $portlet = new IPMVC_Portlet_TemplatedContent();
                        $portlet->setResourceUri($uri);
                        $portlet->setResourceSelector($this->ioc->getObject('resource-selector'));
                        $obj->setPortlet($portlet);
                    } 
                    else {
                        $obj->setPortletUri($uri);
                        $obj->setIoC($this->ioc);
                    }
                }
			} 
		}
		
		if( $parentObject!=null ) {
			$parentObject->addChild($obj);
		}
		
		if(!empty($nodeId)) {
			$obj->setId($nodeId);
		}
		
		$overrideId = $node->getAttribute("override-id");
		if( !empty($basePage) ) {
		    if(!empty($overrideId)) {
		        $replacing = $basePage->findId($overrideId);
		        if($replacing && $replacing['node'] instanceof IPMVC_PortletPage_Cell) {
		            $obj->setWidth($replacing['node']->getWidth());
		            $obj->setId($overrideId);
		        }		        
			    $basePage->replaceId($overrideId,$obj);
			}
		}
//IPMVC::log("returning");		
		return $obj;
	}
}