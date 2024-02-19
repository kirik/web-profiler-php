<?php

namespace WebProfilerPhp\Collector;

class Database extends Base
{
    protected const METRICS = [
        'query'      => [
            'title' => 'Query',
            'type'  => 'text',
        ],
        'duration'   => [
            'title' => 'Duration',
            'type'  => 'seconds',
        ],
        'memory'     => [
            'title' => 'Memory',
            'type'  => 'bytes',
        ],
        'rows_count' => [
            'title' => 'Rows',
            'type'  => 'integer',
        ],
    ] + parent::METRICS;

    private $_startTime, $_startMemory;

    final protected function __construct(array $data)
    {
        $this->_startTime = microtime(true);
        $this->_startMemory = memory_get_usage();
        parent::__construct($data);
    }

    final public static function start(string $query): ?self
    {
        if (!self::_isEnabled()) {
            return null;
        }
        return new static([
            'query' => $query,
        ]);
    }

    public function stop(int $rowsCount, array $additional = [])
    {
        $this->_stop([
                'duration'   => microtime(true) - $this->_startTime,
                'memory'     => memory_get_usage() - $this->_startMemory,
                'rows_count' => $rowsCount,
            ] + $additional);
    }
}
