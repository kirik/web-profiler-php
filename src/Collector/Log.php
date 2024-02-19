<?php

namespace Kirik\WebProfilerPhp\Collector;

class Log extends Base
{
    const LEVEL_DEBUG = 'debug',
        LEVEL_WARNING = 'warn',
        LEVEL_ERROR = 'error';

    protected const METRICS = [
        'level'     => [
            'title' => 'Level',
            'type'  => 'string',
        ],
        'message'   => [
            'title' => 'Message',
            'type'  => 'json',
        ],
        'file_line' => [
            'title' => 'File',
            'type'  => 'string',
        ],
        'time'      => [
            'title' => 'Time',
            'type'  => 'unixtime',
        ],
    ];

    protected function __construct(array $data)
    {
        parent::__construct($data);
    }

    public static function dump($data, string $level = self::LEVEL_DEBUG, string $caller = null)
    {
        if (!self::_isEnabled()) {
            return null;
        }
        if ($caller === null) {
            $bt = debug_backtrace();
            $caller = array_shift($bt);
        }

        (new self([
            'level'     => $level,
            'message'   => json_encode($data),
            'file_line' => $caller['file'] . ':' . $caller['line'],
            'time'      => time(),
        ]))->_stop([]);
    }
}
