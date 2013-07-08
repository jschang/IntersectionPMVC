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

ini_set('error_reporting',E_ALL);
ini_set('display_errors',true);
ob_start();

define('REDESIGN_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR.'..');
define('CODE_ROOT',REDESIGN_ROOT.DIRECTORY_SEPARATOR.'includes');
define('CONF_ROOT',REDESIGN_ROOT.DIRECTORY_SEPARATOR.'conf');
define('CODE_EXTENSION','.php');

class IPMVC {
	static private $siteRoot = "";
	static function loadClass($className) {
	    if(strpos($className,'IPMVC_')===0) {
	        $class = substr($className,6);
	    } else $class = $className;
		$parts = explode('_',$class);
		$path = implode(DIRECTORY_SEPARATOR,$parts);
		if( ! class_exists($className) ) {
			$file = CODE_ROOT.DIRECTORY_SEPARATOR.$path.CODE_EXTENSION;
			if( file_exists($file) )
				include_once($file);
			else {
				$file = IPMVC::getSiteRoot().DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.$path.CODE_EXTENSION;
				if( file_exists($file) )
					include_once($file);
			} 
		}
	}
	static function isClass($className,$value) {
		return ( gettype($value) == 'object' && is_a($value,$className) );
	}
	static function getSiteMergedIoC($file) {
		$ioc = new IPMVC_Model_IoCContainer( new IPMVC_Resource_File(CONF_ROOT.DIRECTORY_SEPARATOR.$file) );
		$toMerge = $ioc->getObject('resource-selector')->getResource('site-root://conf'.DIRECTORY_SEPARATOR.$file);
		if( $toMerge )
			$ioc->merge( $toMerge );
		return $ioc;
	}
	static function getSiteRoot() {
		return IPMVC::$siteRoot;
	}
	static function run($siteRoot='.') {
		$ioc = IPMVC::loadAppIoC($siteRoot);

		$response = $ioc->getObject('http-response');
		$request = $ioc->getObject('http-request');
		$router = $ioc->getObject('http-router');
		$controller = $router->route($request);

		if( $controller instanceof IPMVC_Controller ) {
			IPMVC::runController($controller,$request,$response);
		} else {
			$response->setStatusCode(404);
			$response->setBody("404 : Error<br/>No Controller is associated with \"".$request->getUri()."\"");
		}
		return $response;
	}
	static function runController(IPMVC_Controller $controller,IPMVC_Request $request, IPMVC_Response $response) {
		$view = $controller->process($request,$response);
		if(!empty($view)) {
			$view->render($request,$response);
		}
	}
	static function loadAppIoC($siteRoot='.') {
	    IPMVC::$siteRoot = $siteRoot;
		IPMVC::loadClass('IPMVC_Model_IoCContainer');
		IPMVC::loadClass('IPMVC_Resource_File'); 
		$ioc = IPMVC::getSiteMergedIoC('context.xml');
		return $ioc;
	}
}
