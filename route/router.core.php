<?php

class router_core
{
    public $response;

    public function __construct($params = array())
    {
        //Get calling file to route to appropriate router
        $bt = debug_backtrace();
        $caller = array_shift($bt);

        $callingFile = substr($caller['file'], strrpos($caller['file'], '/') + 1);
        $callingFile = substr($callingFile, 0, strpos($callingFile, '.'));
        $className = 'router_' . $callingFile;

        unset($bt);
        unset($caller);

        $router = new $className;

        if(isset($params['action']) && $params['action'])
        {
            if(isset($params['params']) && $params['params'])
            {
                $this->response = $router->{$params['action']}($params['params']);
            }
        }
    }

}