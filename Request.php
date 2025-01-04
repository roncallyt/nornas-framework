<?php

namespace Nornas;

class Request
{
    private $url = array();
    
    public function __construct()
    {
        if (isset($_GET["url"])) {
            $this->url = rtrim($_GET["url"]);
            return;
        }
    }
    
    public function getUrl()
    {
        return $this->url;
    }
}
