<?php

namespace Nornas;

class RouteAgent
{
    protected static $dirs = array();
    protected static $controllers = array();
    protected static $actions = array();
    
    protected function __construct() {}
    
    public static function get()
    {
        return $_GET["url"];
    }
    
    public static function addController($key, $value)
    {
        self::$controllers[$key] = $value;
    }
    
    public static function addAction($key, $value)
    {
        self::$actions[$key] = $value;
    }
    
    public static function addDir($key, $value)
    {
        self::$dirs[$key] = $value;
    }
    
    public static function getController($c)
    {
        if (key_exists($c, self::$controllers)) {
            return self::$controllers[$c];
        }
        
        return array_search($c, self::$controllers);
    }
    
    public static function getAction($a)
    {
        if (key_exists($a, self::$actions)) {
            return self::$actions[$a];
        }
        
        return array_search($a, self::$actions);
    }
    
    public static function getDir($d)
    {
        if (key_exists($d, self::$dirs)) {
            return self::$dirs[$d];
        }
        
        return array_search($d, self::$dirs);
    }
}
