<?php

namespace Nornas;

use Nornas\Database;
use Nornas\LogLevel;

class Logger
{
    protected $level;
    protected $context;
    protected $db;
    protected $table = "logs";

    public static function create (LogLevel $level, $context = array())
    {
        $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);

        $this->level = $level;

        $this->context = $context;
    }

    public function save ()
    {
        return $this->db->create($this->table, $this->context, false);
    }
}
