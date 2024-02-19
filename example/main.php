<?php
chdir('../');

require('vendor/autoload.php');

// starting profiler
WebProfilerPhp\Profiler::start($_SERVER['REQUEST_URI'], []);

\WebProfilerPhp\Collector\Log::dump('test');
\WebProfilerPhp\Collector\PhpInfo::dump();

$span = \WebProfilerPhp\Collector\Database::start('SELECT * FROM users WHERE id = 123');
usleep(rand(100000, 500000));
if ($span !== null) {
    $span->stop(0);
}

// render profiler's UI
echo \WebProfilerPhp\Profiler::render([]);
