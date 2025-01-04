<?php

namespace Nornas;

/**
 * 
 * @package Router
 */

class RouteGroup
{
    /**
     *
     * @var Array $routeGroups
     */
    private static $groups = array();
    
    protected function __construct() {}

    /**
     * 
     * @param String $alias
     * @param String $pattern
     * @param Closure $callback
     * 
     * @return Void
     */
    
    public static function add($alias, $pattern, $callback)
    {
        self::$groups[$alias] = array(
            "pattern" => str_replace("/", "\/", $pattern),
            "callback" => $callback
        );
    }
    
    /**
     * 
     * @param String $alias
     * @return Array
     */
    
    public static function get($alias)
    {
        if (isset(self::$groups[$alias])) {
            return self::$groups[$alias];
        }
    }
    
    public static function getAll()
    {
        return self::$groups;
    }
}
