<?php

namespace Kirik\WebProfilerPhp;

use Kirik\WebProfilerPhp\Collector\Base;

class Request
{
    /** @var float */
    protected $_startTime;
    protected $_data = [
        'request_uri'      => '-',
        'request_headers'  => [],
        'response_headers' => [],
        'peak_memory'      => '-',
        'collectors_data'  => [],
    ];

    /**
     * @param string $requestUri
     * @param array  $requestHeaders
     */
    public function __construct(string $requestUri, array $requestHeaders)
    {
        $this->_data['request_uri'] = $requestUri;
        $this->_data['request_headers'] = $requestHeaders;
        $this->_data['start_time'] = $this->_startTime = microtime(true);
    }

    /**
     * @param array $responseHeaders
     * @return void
     */
    public function stop(array $responseHeaders)
    {
        $this->_data['response_headers'] = $responseHeaders;
        $this->_data['duration'] = microtime(true) - $this->_startTime;
        $this->_data['peak_memory'] = memory_get_peak_usage(true);
    }

    /**
     * @param Base  $collectorClass
     * @param array $data
     * @return void
     */
    public function addSpanData(string $collectorClass, array $data)
    {
        if (!isset($this->_data['collectors_data'][$collectorClass])) {
            $this->_data['collectors_data'][$collectorClass] = [
                'props'    => $collectorClass::getProperties(),
                'duration' => 0,
                'data'     => [],
            ];
        }
        $this->_data['collectors_data'][$collectorClass]['duration'] += $data['__duration'] ?? 0;
        $this->_data['collectors_data'][$collectorClass]['data'][] = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        // cast collectors_data to array
        $this->_data['collectors_data'] = array_values($this->_data['collectors_data']);
        return $this->_data;
    }
}
