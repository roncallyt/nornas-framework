<?php

namespace \Nornas;

use \PDO;

class Database extends PDO
{
	protected $table = "";
	private $columns = array();
	private $constraints = array();
	private $joints	= array();
	private $groupby;
	private $having;
	private $orderby;
	private $query = "";
	private $id;

	public function __construct($type, $host, $name, $user, $pass)
    {
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            1002 => "SET NAMES utf8"
        );
        
        parent::__construct("{$type}:host={$host};dbname={$name};charset=utf8", $user, $pass, $options);
    }

    public function query($type, $table = null, $options = [])
    {
    	$this->id = uniqid(time());

    	switch ($type) {
    		case 'select': {
    			$this->query = "
    				SELECT [DISTINCT]
    					{column} AS {column alias}
    				FROM 
    					{table} AS {table alias}
    				[@join
    					{type} JOIN 
    						{table join} AS {table join alias}
    					[@join:on
    						ON 
    							{junction constraints}
    					endproperty]
    				endjoin]
    				[@where
    					WHERE 
    						{where}
    				endwhere]
    				[@groupby
    					GROUP BY 
    						{grouping columns}
    				endgroupby]
    				[@having
    					HAVING 
    						{having condition}
    				endhaving]
    				[@orderby
    					ORDER BY 
    						{sort condition}
    					[@orderby:sortType
    						{sort type}
    					endproperty]
    				endorderby]
    				[@limit
    					LIMIT 
    						{limit number}
    				endlimit]
    			";

    			break;
    		}
    		
    		default:
    			# code...
    			break;
    	}
    }

    public function columns()
    {

    }

    public function where()
    {

    }

    public function join()
    {

    }

    public function groupby()
    {

    }

    public function having()
    {

    }

    public function orderby()
    {

    }

    public function limit()
    {

    }

    public function execute()
    {

    }

    protected static function getInstance()
    {
        $instance = null;
        
        if (!$instance) {
            $instance = new Route();
        }
        
        return $instance;
    }
}

SELECT DISTINCT 
	id, nome, cpf 
FROM 
	cliente c 
INNER JOIN 
	empresa e 
ON 
	c.id = e.id 
WHERE 
	(e.id = '2' AND c.id = '2') 
OR 
	(e.id >= '2') 
GROUP BY 
	e.id 
HAVING 
	c.id >= '2' 
ORDER BY 
	e.id
























$dbl = new \Nornas\Database();

$dbl->query("select", [$table => "t"], [Database::DUPLICATION])
	->columns("column1", "column2")
	->where(
		"($1 && $2) || $3",
		[
			"key1" => "value1",
			"key2" => "value2",
			"key3" => "value3"
		])
	->join("in", function ($dbl) {
		
	})
	->groupby("id")
	->having("id = 2")
	->orderby("id", "name")
	->limit(100)
	->execute();
