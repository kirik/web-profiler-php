<?php

namespace Kirik\WebProfilerPhp\Collector;

use Kirik\WebProfilerPhp\Profiler;

abstract class Base
{
    // title, class name will be used if null
    protected const TITLE = null;
    // color which will represent this type of collector (will be fixed-random by default)
    // refer to https://github.com/kirik/web-profiler-ui/blob/main/README.md#internals
    protected const CSS_COLOR = null;
    // which template will be used to render collector metrics
    // refer to https://github.com/kirik/web-profiler-ui/blob/main/README.md#internals
    protected const TEMPLATE = 'table';

    // key-value array with metric objects
    // can be redefined; can be extended as + parent::METRICS - to get final list at compile time (saving some CPU)
    protected const METRICS = [
        // for feature waterfall (aka timeline) implementation
        '__start_time' => [
            'title' => 'Start time',
            'type'  => 'seconds',
        ],
        '__duration'   => [
            'title' => 'Duration',
            'type'  => 'seconds',
        ],
    ];
    private static $_enabled;

    private $_data;

    protected function __construct(array $data)
    {
        if (isset(static::METRICS['__start_time'])) {
            $data['__start_time'] = microtime(true);
        }
        $this->_data = $data;
    }

    final protected static function _isEnabled(): bool
    {
        if (self::$_enabled === null) {
            self::$_enabled = Profiler::isEnabled();
        }
        return self::$_enabled;
    }

    final public static function getProperties(): array
    {
        $title = static::TITLE;
        if ($title === null) {
            $classParts = explode('\\', static::class);
            $title = end($classParts);
        }
        $return = [
            'title'    => $title,
            'template' => static::TEMPLATE,
            'metrics'  => static::METRICS,
        ];
        if (static::CSS_COLOR !== null) {
            $return['cssColor'] = static::CSS_COLOR;
        }
        return $return;
    }

    final protected function _stop(array $data)
    {
        if (isset(static::METRICS['__duration'])) {
            $data['__duration'] = microtime(true) - $this->_data['__start_time'];
        }
        Profiler::addSpanData(get_class($this), $this->_data + $data);
    }
}
