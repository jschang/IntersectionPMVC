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

IPMVC::loadClass('IPMVC_IoC_ParamValuePreProcessor');

class IPMVC_IoC_ParamValuePreProcessor_Constants implements IPMVC_IoC_ParamValuePreProcessor {
    private $debug = false;
    public function setConstants(IPMVC_Model_Constants $constants) {
        $this->constants = $constants;
    }
    public function process($value) {
        $this->log('process:'.$value);
        if( ($r="no match") && preg_match_all('/(\$\{([^}]+)\})+/',$value,$matches) 
            && ($r="no constants") && isset($this->constants) ) {
                $this->log('matches:'.implode(',',$matches[1]));
                foreach($matches[1] as $idx=>$match) {
                        $value = str_replace($match,$this->constants->get($matches[2][$idx]),$value);
                }
                return $value;
        } else {
          $this->log("reason:".$r);
        }
        return $value;
    }
    private function log($msg) {
        if($this->debug) {
          error_log($msg);
        }
    }
}
