<?php

namespace Kirik\WebProfilerPhp\Collector;

class PhpInfo extends Base
{
    protected const TEMPLATE = 'html';
    protected const METRICS = [
        'phpinfo' => [
            'title' => 'PHP info',
        ],
    ];

    protected function __construct(array $data)
    {
        parent::__construct($data);
    }

    public static function dump()
    {
        if (!self::_isEnabled()) {
            return null;
        }
        ob_start();
        phpinfo();
        $phpinfo = ob_get_clean();
        if (preg_match('/<style[^>]+>(.+?)<\/style>/s', $phpinfo, $o)) {
            // wrap phpinfo output to _phpinfo_container class to avoid profiler's css to be broken
            $phpinfo = strtr($phpinfo, [
                '<div class="center">' => '<div class="center _phpinfo_container">',
                $o[1]                  => preg_replace('/^(.+)/m', '._phpinfo_container $1', preg_replace('/^body.+/m', '', $o[1])),
            ]);
        } else {
            $phpinfo = '<h1>Could not prepare phpinfo(), see: ' . __FILE__ . '</h1>';
        }
        (new self([
            'phpinfo' => $phpinfo,
        ]))->_stop([]);
    }
}
