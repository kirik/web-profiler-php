<?php
chdir('../');

require('vendor/autoload.php');

class MyCustomLogCollector extends \WebProfilerPhp\Collector\Log
{

}


class MyCustomDbCollector extends \WebProfilerPhp\Collector\Database
{
    protected const TITLE = 'Db name';
    protected const METRICS = [
        'rnd_int' => [
            'title' => 'RndInt',
            'type'  => 'integer',
        ],
    ] + parent::METRICS;

    public static function startWithRandomInt(string $query, int $rnd)
    {
        if (!self::_isEnabled()) {
            return null;
        }
        return new static([
            'rnd_int' => $rnd,
            'query'   => $query,
        ]);
    }
}

// starting profiler
\WebProfilerPhp\Profiler::start($_SERVER['REQUEST_URI'], []);

$span = MyCustomDbCollector::startWithRandomInt('Some query', rand(0, 10000));
if ($span !== null) {
    $span->stop(0);
}

$span = MyCustomLogCollector::dump(['key' => 'value']);
if ($span !== null) {
    $span->stop(0);
}

// render profiler's UI
echo \WebProfilerPhp\Profiler::render([]);
