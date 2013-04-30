<?php

class Util_Cli {
    static public function parse($config, $arguments) {
        $val = null;
        $ret = array();
        while(($val = array_shift($arguments)) && !empty($val)) {
            
            if($val{0}=='-') {
                if(($parts=explode('=',$val)) && ($thisConfig=self::findConfig($config,$parts[0]))) {
                    // must be an option
                    if($thisConfig['hasValue']) {
                        if(count($parts)==1) {
                            $value = array_shift($arguments);
                        } else {
                            $value = implode('=',array_slice($parts,1));
                        }
                        $ret['opts'][$thisConfig['name']]=$value;
                    } else {
                        $ret['opts'][$thisConfig['name']]=true;
                    }
                } else {
                    $ret['args'][]=$val;
                }
            } else {
                // if we're not in an option,
                // then this may be a sequenced value
                $ret['args'][]=$val;
            }
        }
        return $ret;
    }
    static public function findConfig($config,$opt) {
        foreach($config as $name=>$conf) {
            if(in_array($opt,$conf['expr'])) {
                return array_merge(array('name'=>$name),$conf);
            }
        }
        return null;
    }
}

if(!empty($argv[0]) && basename($argv[0])=='Cli.php') {
    $ret = Util_Cli::parse(
        array(
            'add'=>array(
                'expr'=>array('-a','--add'),
                'hasValue'=>true
            ),
            'original'=>array(
                'expr'=>array('-o','--original'),
                'hasValue'=>true
            ),
            'destination'=>array(
                'expr'=>array('-d','--destination'),
                'hasValue'=>true
            ),
        ),
        array('-a','tempting','another this','-o','filename','-d=should be split','-spatula','tasty','-barf=not tasty')
    );
    print_r($ret);
}
