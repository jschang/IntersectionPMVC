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

JVS::loadClass('Resource_Content');

class Model_Constants {
    
    private $constants = array();
    private $section = null;
    
    public function __construct(Resource_Content $content, $section='root') {
        
        $this->section=$section;        
        // check memcache for the content
        
        // else reload
        $tempFile = tempnam(sys_get_temp_dir(), 'redesign');
        file_put_contents($tempFile,$content->getContent());
        $this->constants = $this->parseConstantsFile($tempFile,$section);
        unlink($tempFile);
    }
    
    public function get($var,$vars=array()) {
        $val = @$this->constants[$this->section]['data'][$var];
        foreach($vars as $k=>$v) {
            $val = str_replace('@{'.$k.'}',$v,$val);
        }
        return $val;
    }
    
    private function parseConstantsFile($fileName, $section) {
        $config = parse_ini_file($fileName,true);
        // first load in each individually
        foreach($config as $k=>$vals) {
            $parts = explode(':',$k);
            foreach($parts as $i=>$v) {
                $parts[$i]=trim($v);
            }
            $stash[$parts[0]]=array('data'=>$vals,'parts'=>$parts);
        }
        foreach($stash as $section=>$vals) {
            $result[$section] = $this->mergeParent($stash,$vals);
        }
        return $result;
    }
    
    private function mergeParent($stash, $config=null) {
        if(count($config['parts'])!=1) {
            $parents = $this->mergeParent($stash,$stash[$config['parts'][1]]);
            $config['data'] = array_merge($parents['data'],$config['data']);
        }
        return $config;
    }
}
