<?php

/**
 * Class for making database connections conveniently configurable in context.xml
 */
class Database_Connection_Factory {
    private $constants = null;
    public function __construct(Model_Constants $constants, $constantsPrefix) {
        $this->constants = $constants;
        $this->constantsPrefix = $constantsPrefix;
    }
    public function newConnection($type=null) {
        /*print_r(array($this->constantsPrefix.($type!=null?'.'.$type:'').'.connectionString',
            $this->constantsPrefix.($type!=null?'.'.$type:'').'.username',
            $this->constantsPrefix.($type!=null?'.'.$type:'').'.password'));
        print_r(array($this->constants->get($this->constantsPrefix.($type!=null?'.'.$type:'').'.connectionString'),
            $this->constants->get($this->constantsPrefix.($type!=null?'.'.$type:'').'.username'),
            $this->constants->get($this->constantsPrefix.($type!=null?'.'.$type:'').'.password')));*/
        return new PDO(
            $this->constants->get($this->constantsPrefix.($type!=null?'.'.$type:'').'.connectionString'),
            $this->constants->get($this->constantsPrefix.($type!=null?'.'.$type:'').'.username'),
            $this->constants->get($this->constantsPrefix.($type!=null?'.'.$type:'').'.password')
            );
    }
}
