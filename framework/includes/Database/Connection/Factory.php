<?php

/**
 * Class for making database connections conveniently configurable in context.xml
 */
class Database_Connection_Factory {
    private $constants = null;
    public function __construct(Model_Constants $constants, $constantsPrefix) {
        $this->constants = $constants;
    }
    public function newConnection($type=null) {
        return new PDO(
            $this->constants->get($constantsPrefix.($type!=null?'.'.$type:'').'.connectionString'),
            $this->constants->get($constantsPrefix.($type!=null?'.'.$type:'').'.username'),
            $this->constants->get($constantsPrefix.($type!=null?'.'.$type:'').'.password')
            );
    }
}
