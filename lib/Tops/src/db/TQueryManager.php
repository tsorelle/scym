<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/24/2015
 * Time: 5:20 AM
 */

namespace Tops\db;

use \Doctrine\DBAL\DriverManager;


/**
 * Class TQueryManager
 * @package Tops\db
 *
 * This class is a wrapper for Doctrine DBAL using Tops database configuration
 * see http://doctrine-dbal.readthedocs.org/en/latest/index.html
 *
 */
class TQueryManager {

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function __construct($type='application') {
        $this->connection = self::GetConnection($type);
    }

    /**
     * @param string $host
     * @return \Doctrine\DBAL\Connection
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function GetConnection($type='application') {
        $config = TDbConfiguration::GetConfiguration();
        $databaseId = $config->Value("type/$type");
        $connectionParams = $config->Value("connections/$databaseId");
        $result = DriverManager::getConnection($connectionParams);
        return $result;
    }

    private function createStatement($sql,$argcount=0,$arguments=null) {
        $statement = $this->connection->prepare($sql);
        if ($argcount > 1) {
            for ($i = 1; $i < $argcount; $i++) {
                $statement->bindValue($i, $arguments[$i]);
            }
        }
        return $statement;
    }

    /**
     * @param $sql
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getStatement($sql) {
        $statement = $this->createStatement($sql,func_num_args(),func_get_args());
        return $statement;
    }
    /**
     * @param $sql
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executeStatement($sql) {
        $statement = $this->createStatement($sql,func_num_args(),func_get_args());
        $statement->execute();
        return $statement;
    }

    public function executeScaler($columnName, $tableName, $whereClause='') {
        $sql = "SELECT $columnName FROM $tableName";
        if (!empty($whereClause)) {
            $sql .=  " WHERE $whereClause";
        }
        $params = func_get_args();
        array_shift($params); // skip columnane
        array_shift($params); // skip tablename
        array_shift($params); // skip whereClause
        $result = $this->connection->fetchColumn($sql,$params);
        return $result;
    }


    /**
     * @param string $type  Database type used in Tops configuration
     * @return \Doctrine\DBAL\Query\QueryBuilder
     *
     * Use this in context all operations use a QueryBuilder and only one is required.
     */
    public static function GetQueryBuilder($type='application')
    {
        $connection = self::GetConnection($type);
        return $connection->createQueryBuilder();

    }


    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     *
     * Use this in an operation where several database operations use the same connection.
     */
    public function createQueryBuilder() {
        return $this->connection->createQueryBuilder();
    }

    public function insert($tableName,$values, $autoincrement = true) {
        $count = $this->connection->insert($tableName,$values);
        if ($autoincrement && $count == 1) {
            return $this->connection->lastInsertId();
        }
        return $count;
    }




}