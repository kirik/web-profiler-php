<?php
chdir('../');

require('vendor/autoload.php');

// starting profiler
\Kirik\WebProfilerPhp\Profiler::start($_SERVER['REQUEST_URI'], []);

\Kirik\WebProfilerPhp\Collector\Log::dump('test');
\Kirik\WebProfilerPhp\Collector\PhpInfo::dump();

$span = \Kirik\WebProfilerPhp\Collector\Database::start('SELECT * FROM users WHERE id = 123');
usleep(rand(100000, 500000));
if ($span !== null) {
    $span->stop(0);
}

// render profiler's UI
echo \Kirik\WebProfilerPhp\Profiler::render([]);
