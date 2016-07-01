<?php

/**
 * Class for making database connections conveniently configurable in context.xml
 */
class IPMVC_Database_Connection_Factory {
    private $constants = null;
    public function __construct(IPMVC_Model_Constants $constants, $constantsPrefix) {
        $this->constants = $constants;
        $this->constantsPrefix = $constantsPrefix;
    }
    public function newConnection($type=null) {
        return new PDO(
            $this->constants->get($this->constantsPrefix.($type!=null?'.'.$type:'').'.connectionString'),
            $this->constants->get($this->constantsPrefix.($type!=null?'.'.$type:'').'.username'),
            $this->constants->get($this->constantsPrefix.($type!=null?'.'.$type:'').'.password')
            );
    }
}
