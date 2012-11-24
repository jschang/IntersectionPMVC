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

class Model_Messages {
    
    private $messages = array();
    private $lang = 'en';
    
    public function __construct(Resource_Content $content, $lang='en') {
        
        $this->lang = $lang;
        
        // check memcache for the content
        
        // else reload
        $tempFile = tempnam(sys_get_temp_dir(), 'redesign');
        file_put_contents($tempFile,$content->getContent());
        $this->messages = parse_ini_file($tempFile,true);
        unlink($tempFile);
    }
    
    public function get($var) {
        return @$this->messages[$this->lang][$var];
    }
}
