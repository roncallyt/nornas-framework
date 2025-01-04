<?php

namespace Nornas;

use Nornas\RouteGroup;

class Route
{
    /**
     *
     * @var Array $routes 
     */
    private static $routes = array();
    
    /**
     *
     * @var Array $globals
     */
    private static $globals = array();
    
    protected function __construct() {}
    
    /**
     * 
     * Método responsável por adicionar uma rota.
     * 
     * @param String $url
     * @return Object
     */
    
    public static function add($url)
    {
        $routes = &self::$routes;
        
        if (key($routes) !== $url) {
            next($routes);
        }
        
        $routes[$url] = array();
        
        return self::getInstance();
    }
    
    /**
     * 
     * Método responsável pelo cadastro de restrições da rota.
     * 
     * @param Array $constraints
     * @return Object
     */
    
    public function where($constraints)
    {
        $routes = &self::$routes;
        
        $key = key($routes);
        
        $routes[$key] = array_merge($routes[$key], array(
            "constraints" => $constraints
        ));
        
        return self::getInstance();
    }

    public function defaults($values)
    {
        $routes = &self::$routes;

        $key = key($routes);

        $routes[$key] = array_merge($routes[$key], array(
            "default" => $values
        ));

        return self::getInstance();
    }

    /**
     * 
     * Método responsável pelo cadastro de um alias/apelido/nome para a rota.
     * 
     * @param String $pattern
     * @return Object
     */
    
    public function alias($pattern)
    {
        $routes = &self::$routes;
        
        $key = key($routes);
        
        $routes[$key] = array_merge($routes[$key], array(
            "alias" => $pattern
        ));
        
        return self::getInstance();
    }
    
    /**
     * 
     * Método responsável pelo registro de rotas em um ou mais grupos.
     * 
     * @param String $group
     * @return Void
     */
    
    public function register($group)
    {
        $routes = &self::$routes;
        
        $key = key($routes);
        
        if (!RouteGroup::get($group)) {
            if ($group === "all")   {
                $group = array();
                $groups = RouteGroup::getAll();
                
                foreach ($groups as $keyg => $g) {
                    $group[] = $keyg;
                }
            } else {
                self::error("O grupo '{$group}' não foi encontrado.");
            }
        }

        $routes[$key] = array_merge($routes[$key], array(
            "group" => $group
        ));

        return self::getInstance();
    }
    
    /**
     * 
     * @param String $key
     * @param String $value
     */
    
    public static function setGlobal($key, $value)
    {
        self::$globals[$key] = $value;
    }
    
    /**
     * 
     * Método responsável por retornar uma instância desta classe.
     * 
     * @return Route $instance
     */
    
    protected static function getInstance()
    {
        $instance = null;
        
        if (!$instance) {
            $instance = new Route();
        }
        
        return $instance;
    }
    
    /**
     * 
     * Método responsável por retornar todas as rotas cadastradas.
     * 
     * @return Array $routes
     */
    
    public static function getAll()
    {
        return self::$routes;
    }
    
    public static function getGlobals()
    {
        return self::$globals;
    }
    
    /**
     * 
     * Método responsável por tratamento de erros relacionados à classe.
     * 
     * @param String $msg
     * @throws \Exception
     * @return Void
     */
    
    protected static function error($msg)
    {
        throw new \Exception($msg);
    }
    
}
