<?php

namespace WebProfilerPhp\Proxy;

use WebProfilerPhp\Collector\Database;
use PDO as PDOOriginal;

class PDO extends PDOOriginal
{
    /**
     * @param $query
     * @param $fetchMode
     * @param $arg3
     * @param $ctorargs
     * @return false|\PDOStatement
     */
    public function query($query, $fetchMode = null, $arg3 = null, $ctorargs = [])
    {
        $profiler = Database::start($query);
        if ($fetchMode === null) {
            $statement = PDOOriginal::query($query);
        } elseif (is_int($arg3)) {
            $statement = PDOOriginal::query($query, $fetchMode, $arg3);
        } elseif (is_object($arg3)) {
            $statement = PDOOriginal::query($query, $fetchMode, $arg3, $ctorargs);
        }
        if ($profiler !== null) {
            $profiler->stop($statement->rowCount());
        }
        return $statement;
    }

    /**
     * @param \PDOStatement $statement
     * @return false|int
     */
    public function exec($statement)
    {
        $profiler = Database::start($statement->queryString);
        $result = parent::exec($statement);
        if ($profiler !== null) {
            $profiler->stop(0);
        }
        return $result;
    }
}
