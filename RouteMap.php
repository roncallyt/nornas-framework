<?php

namespace Nornas;

use Nornas\RouteAgent,
    Nornas\RouteGroup,
    Nornas\Route;

class RouteMap
{
    /**
     *
     * @var Array $controller Controladores
     */

    protected static $controller = "";

    /**
     *
     * @var Array $action Ações
     */

    protected static $action = "";

    /**
     *
     * @var Array $args Argumentos
     */

    protected static $args = array();


    /**
     *
     * Método construtor
     */

    protected function __construct() {}

    /**
     *
     * Método Run
     */
    
    public static function run()
    {
        if (Common::isEmpty(Route::getAll())) {
            return false;
        }
        
        $routes = Route::getAll();

        $url = RouteAgent::get();
        
        foreach ($routes as $key => $route) {
            $pattern = self::setUpPattern($key, $route);
        }

        $valid = false;
       
        while (!$valid) {
            if (isset($pattern["alias"]) && !$valid) {
                $valid = preg_match($pattern["alias"], $url);
            }

            if (!$valid) {
                $valid = preg_match($pattern["general"], $url);
            }

            if (!$valid) {
                $url = "";
                $valid = true;
            }
        }
       
        if ($valid) {
            $segments = explode("/", $url);

            foreach ($segments as $key => $value) {
                if (Common::isEmpty($value)) {
                    unset($segments[$key]);
                }
            }

            if (
                (
                    is_array($route["group"]) && 
                    in_array(RouteAgent::getDir($segments[0]), $route["group"])
                ) 
                || RouteAgent::getDir($segments[0]) == $route["group"]
            ) {
                $dir = array_shift($segments); 
            } else {
                $dir = "";
            }

            $validate = is_array($segments) && !Common::isEmpty($segments);

            if ($validate && RouteAgent::getController($segments[0])) {
                if (is_array($route["group"])) {
                    foreach ($route["group"] as $key => $value) {
                        if (RouteAgent::getDir($dir) == $value) {
                            $callback = RouteGroup::get($value)["callback"];
                            self::$controller = $callback(RouteAgent::getController(array_shift($segments)));
                        }
                    }
                } else {
                    if (RouteAgent::getDir($dir) == $route["group"]) {
                        $callback = RouteGroup::get($route["group"])["callback"];
                        self::$controller = $callback(RouteAgent::getController(array_shift($segments)));
                    }
                }
            } elseif (isset($route["default"]["controller"])) {
                if (is_array($route["group"])) {
                    foreach ($route["group"] as $key => $value) {
                        if (RouteAgent::getDir($dir) == $value) {
                            $callback = RouteGroup::get($value)["callback"];
                            self::$controller = $callback($route["default"]["controller"]);
                        }
                    }
                } else {
                    if (RouteAgent::getDir($dir) == $route["group"]) {
                        $callback = RouteGroup::get($route["group"])["callback"];
                        self::$controller = $callback($route["default"]["controller"]);
                    }
                }
            } else {
                if (is_array($route["group"])) {
                    foreach ($route["group"] as $key => $value) {
                        if (RouteAgent::getDir($dir) == $value) {
                            $callback = RouteGroup::get($value)["callback"];
                            self::$controller = $callback("index");
                        }
                    }
                } else {
                    if (RouteAgent::getDir($dir) == $route["group"]) {
                        $callback = RouteGroup::get($route["group"])["callback"];
                        self::$controller = $callback("index");
                    }
                }
            }

            $validate = is_array($segments) && !Common::isEmpty($segments);

            if ($validate && RouteAgent::getAction($segments[0])) {
                self::$action = "action_" . RouteAgent::getAction(array_shift($segments));
            } elseif (isset($route["default"]["action"])) {
                self::$action = "action_" . $route["default"]["action"];
            } else {
                self::$action = "action_" . "main";
            }

            self::$args = (is_array($segments))
                        ? $segments
                        : array();

            self::call();
        }
    }
    
    /**
     *
     * @param String $key Padrão geral de validação
     * @param Array $route Rotas cadastradas no sistema
     */

    protected static function setUpPattern($key, $route)
    {
        $globals = Route::getGlobals();
        
        if (isset($route["alias"])) {
            $alias = $route["alias"];
        }

        if (isset($route["constraints"])) {
            foreach ($route["constraints"] as $constraint => $value) {
                if (isset($value["pattern"])) {
                    $key = str_replace("{" . $constraint . "}", $value["pattern"], $key);
                    if (isset($route["alias"])) {
                        $alias = str_replace("{" . $constraint . "}", $value["pattern"], $alias);
                    }
                }
            }
        }

        foreach ($globals as $globalKey => $globalValue) {
            $key = str_replace($globalKey, $globalValue, $key);
            if (isset($route["alias"])) {
                $alias = str_replace($globalKey, $globalValue, $alias);
            }
        }
        
        if (isset($key) && $key) {
            $key = str_replace("/", "\/", rtrim($key, "/"));
            foreach ($route["group"] as $g) {
                if (($g !== "root") && strstr(RouteAgent::get(), RouteAgent::getDir($g))) {
                    $result["general"] = "/^" . RouteGroup::get($g)["pattern"] . $key . "\/?$/";
                } else {
                    $result["general"] = "/^" . RouteGroup::get("root")["pattern"] . $key . "\/?$/";
                }
            }
        }
        
        if (isset($alias) && $alias) {
            $alias = str_replace("/", "\/", rtrim($alias, "/"));
            foreach ($route["group"] as $g) {
                if (($g !== "root") && strstr(RouteAgent::get(), RouteAgent::getDir($g))) {
                    $result["alias"] = "/^" . RouteGroup::get($g)["pattern"] . $alias . "\/?$/";
                } else {
                    $result["alias"] = "/^" . RouteGroup::get("root")["pattern"] . $alias . "\/?$/";
                }
            }
        }
        
        return $result;
    }

    /**
     *
     * Método de chamada aos controladores.
     */

    protected static function call()
    {
        $c = self::$controller;
        $a = self::$action;

        $c = new $c;

        if (!is_callable(array($c, $a))) {
            self::error("O método '{$a}' não foi encontrado.");
            return false;
        }

        call_user_func_array(array($c, $a), self::$args);
    }

    /**
     *
     * @param String $msg Mensagem de erro
     */

    protected static function error($msg)
    {
        throw new \Exception($msg);
    }
}
