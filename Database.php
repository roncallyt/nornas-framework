<?php

namespace Nornas;

use \PDO;

/**
 *
 *
 */

class Database extends PDO
{
    protected $table = "";

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

    public function create($table)
    {
        $params = func_get_args();

        $query = "INSERT INTO {$table} (";

        foreach ($params[1] as $name => $value) {
            $query .= "{$name}";
            $keys = array_keys($params[1]);
            if (end($keys) !== $name) {
                $query .= ", ";
            } else {
                $query .= ") ";
            }
        }

        $query .= "VALUES (";

        foreach ($params[1] as $name => $value) {
            $query .= ":{$name}";
            $keys = array_keys($params[1]);
            if (end($keys) !== $name) {
                $query .= ", ";
            } else {
                $query .= ") ";
            }
        }

        try {
            $stmt = $this->prepare($query);

            foreach ($params[1] as $name => $value) {
                $pdoParam = (is_int($value)) ? PDO::PARAM_INT : PDO::PARAM_STR;
                
                $stmt->bindValue(":{$name}", $value, $pdoParam);
            }

            $result = $stmt->execute();

            if ($params[2]) {
                return $this->lastInsertId();
            } else {
                return $result;
            }
        } catch (\PDOException $e) {
            self::error($e->errorInfo[0], $e->errorInfo[1], $e->getMessage());
        }
    }
    
    public function retrieve($table)
    {
        $params = func_get_args();
        
        $query = "SELECT ";

        foreach ($params[2] as $selectableColumns) {
            $query .= $selectableColumns;
            $values = array_values($params[2]);
            if (end($values) !== $selectableColumns) {
                $query .= ",";
            }
            $query .= " ";
        }

        $query .= "FROM {$table} WHERE ";

        foreach ($params[1] as $name => $value) {
            $query .= "{$name} = :{$name}";
            $keys = array_keys($params[1]);
            if (end($keys) !== $name) {
                $query .= " AND ";
            }
        }

        try {
            $stmt = $this->prepare($query);
            
            foreach ($params[1] as $name => $value) {
                $pdoParam = (is_int($value)) ? PDO::PARAM_INT : PDO::PARAM_STR;
                
                $stmt->bindValue(":{$name}", $value, $pdoParam);
            }

            $stmt->execute();

            if ($params[3]) {
                return $stmt->fetchAll();
            } else {
                return $stmt->fetch();
            }
        } catch (\PDOException $e) {
            self::error($e->errorInfo[0], $e->errorInfo[1]);
        }
    }
    
    public function update($table)
    {
        $params = func_get_args();

        $query = "UPDATE {$table} SET ";

        foreach ($params[2] as $name => $value) {
            $query .= "{$name} = :{$name}";
            $keys = array_keys($params[2]);
            if (end($keys) !== $name) {
                $query .= ",";
            }
            $query .= " ";
        }

        $query .= "WHERE ";

        foreach ($params[1] as $name => $value) {
            $query .= "{$name} = :{$name}";
            $keys = array_keys($params[1]);
            if (end($keys) !== $name) {
                $query .= " AND ";
            }
        }

        try {
            $stmt = $this->prepare($query);

            foreach ($params[2] as $name => $value) {
                $pdoParam = (is_int($value)) ? PDO::PARAM_INT : PDO::PARAM_STR;
                
                $stmt->bindValue(":{$name}", $value, $pdoParam);
            }

            foreach ($params[1] as $name => $value) {
                $pdoParam = (is_int($value)) ? PDO::PARAM_INT : PDO::PARAM_STR;
                
                $stmt->bindValue(":{$name}", $value, $pdoParam);
            }

            return $stmt->execute();
        } catch (\PDOException $e) {
            self::error($e->errorInfo[0], $e->errorInfo[1], $e->getMessage());
        }
    }
    
    public function delete($table, $referenceColumns)
    {
        $query = "DELETE FROM {$table} WHERE ";

        foreach ($referenceColumns as $name => $value) {
            $query .= "{$name} = :{$name}";
            $keys = array_keys($referenceColumns);
            if (end($keys) !== $name) {
                $query .= " AND ";
            }
        }

        try {
            $stmt = $this->prepare($query);

            foreach ($referenceColumns as $name => $value) {
                $pdoParam = (is_int($value)) ? PDO::PARAM_INT : PDO::PARAM_STR;
               
                $stmt->bindValue(":{$name}", $value, $pdoParam);
            }

            return $stmt->execute();
        } catch (\PDOException $e) {
            self::error($e->errorInfo[0], $e->errorInfo[1]);
        }
    }
    
    public function fetchAll($table, $order)
    {
        $query = "SELECT * FROM {$table} ";

        if (!empty($order)) {
            $query .= "ORDER BY {$order}";
        }

        try {
            $stmt = $this->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            self::error($e->errorInfo[0], $e->errorInfo[1]);
        }
    }

    public function queryJoin($referenceColumns, $query, $allRecords)
    {
        try {
            $stmt = $this->prepare($query);

            if ($referenceColumns !== array()) {
                foreach ($referenceColumns as $key => $value) {
                    $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
                }
            }

            $stmt->execute();

            if ($allRecords) {
                return $stmt->fetchAll();
            } else {
                return $stmt->fetch();
            }
        } catch (\PDOException $e) {
            self::error($e->errorInfo[0], $e->errorInfo[1]);
        }
    }
    
    protected function error($sqlState, $errorCode, $message = null)
    {
        switch ($errorCode) {
            case '1451':
                throw new \Exception(
                    "SQLSTATE: {$sqlState}; Código: {$errorCode}; Mensagem: Não é possível deletar este registro."
                );
            case '1064':
                throw new \Exception(
                    "SQLSTATE: {$sqlState}; Código: {$errorCode}; Mensagem: Você tem um erro na sintaxe SQL."
                );
            case '1102':
                throw new \Exception(
                    "SQLSTATE: {$sqlState}; Código: {$errorCode}; Mensagem: Nome da base de dados incorreto."
                );
            case '1103':
                throw new \Exception(
                    "SQLSTATE: {$sqlState}; Código: {$errorCode}; Mensagem: Nome da tabela incorreto."
                );
            case '1146':
                throw new \Exception(
                    "SQLSTATE: {$sqlState}; Código: {$errorCode}; Mensagem: A tabela não existe."
                );
            case '1051':
                throw new \Exception(
                    "SQLSTATE: {$sqlState}; Código: {$errorCode}; Mensagem: Tabela desconhecida."
                );
            case '1054':
                throw new \Exception(
                    "SQLSTATE: {$sqlState}; Código: {$errorCode}; Mensagem: " . $message
                );
            default:
                throw new \Exception(
                    "SQLSTATE: {$sqlState}; Código: {$errorCode}; Mensagem: " . $message
                );
        }
    }
}
