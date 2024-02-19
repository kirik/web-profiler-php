<?php

namespace Kirik\WebProfilerPhp;

use Kirik\WebProfilerPhp\Collector\Base;
use Kirik\WebProfilerUi\Renderer;

class Profiler
{
    /** @var Request */
    private static $_request;

    public static function isEnabled(): bool
    {
        return self::$_request !== null;
    }

    /**
     * @param string $requestUri
     * @param array  $requestHeaders
     * @return Request|void
     */
    public static function start(string $requestUri, array $requestHeaders): bool
    {
        if (self::isEnabled()) {
            trigger_error('PHPProfiler request already started');
            return false;
        }
        self::$_request = new Request($requestUri, $requestHeaders);
        return true;
    }

    /**
     * @param array $responseHeaders
     * @return bool
     */
    protected static function _stop(array $responseHeaders): ?Request
    {
        if (!self::isEnabled()) {
            return null;
        }
        $request = self::$_request;
        $request->stop($responseHeaders);
        self::$_request = null;
        return $request;
    }

    /**
     * @param Base  $collectorClassName
     * @param array $data
     * @return void
     */
    public static function addSpanData(string $collectorClassName, array $data)
    {
        self::$_request->addSpanData($collectorClassName, $data);
    }

    public static function addProfilerToJson(array $data, array $stopResponseHeaders = []): array
    {
        if (self::isEnabled()) {
            $data['__profiler'] = self::_stop($stopResponseHeaders)->getData();
        }
        return $data;
    }

    /**
     * @param bool $minify
     * @return string
     */
    public static function render(array $stopResponseHeaders = null, bool $minify = true): string
    {
        if (!self::isEnabled()) {
            return '';
        }
        $data = self::_stop($stopResponseHeaders)->getData();
        if (empty($data)) {
            trigger_error('PHPProfiler empty data');
            return '';
        }
        self::$_request = null;

        return Renderer::render($data, $minify);
    }
}
