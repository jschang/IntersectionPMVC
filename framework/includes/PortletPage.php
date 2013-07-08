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

IPMVC::loadClass('IPMVC_Resource');
IPMVC::loadClass('IPMVC_PortletPage_Component');
IPMVC::loadClass('IPMVC_PortletPage_Cell');
IPMVC::loadClass('IPMVC_PortletPage_Column');
IPMVC::loadClass('IPMVC_PortletPage_Row');
IPMVC::loadClass('IPMVC_PortletPage_Portlet');

interface IPMVC_PortletPage extends IPMVC_Resource {
}