<?php

namespace Nornas;

class Session
{
    public static function init()
    {
        session_start();
        session_regenerate_id();
    }
    
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }
    
    public static function del($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        } else {
            return false;
        }
    }
    
    public static function destroy()
    {
        session_destroy();
    }
}
