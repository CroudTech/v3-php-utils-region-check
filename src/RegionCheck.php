<?php

namespace CroudTech;

use GuzzleHttp\Client as GuzzleClient;

class RegionCheck
{
    /**
     * HTTP client
     * 
     * @var GuzzleHttp\GuzzleClient
     */
    protected $client;

    /**
     * HTTP path
     * 
     * @var string
     */
    protected $path;


    /**
     * Called when the class in instantiated
     * 
     * @param GuzzleHttp\GuzzleClient $httpClient HTTP client
     * @param string $path  HTTP path
     * 
     * @return null
     */
    public function __construct(GuzzleClient $httpClient, $path = null)
    {
        $this->httpClient = $httpClient;
        $this->path = $path;
    }

    public function query()
    {
        return $this->httpClient->get($this->path, ['timeout' => 2]);
    }

    /**
     * Performs check to determine if region is master
     * 
     * @return bool  True is region is master
     */
    public function isMaster(): bool
    {
        if (is_null($this->path) || empty($this->path)) {
            return true;
        }
        try {
            $data = $this->getData(
                $this->query()
            );
        } catch (\Exception $e) {
            throw new \Exception('Invalid response');
        }

        if (!isset($data['IsMaster'])) {
            throw new \Exception('Invalid results');
        }

        return $data['IsMaster'];
    }


    /**
     * Error handling
     * 
     * @param GuzzleHttp\Psr7\Response $reponse  HTTP response
     * 
     * @return array  Response Data
     */
    public function getData($response): array
    {
        $data = json_decode(
            $response->getBody()->getContents(),
            true
        );

        if (!$data) {
            return [];
        }

        return $data;
    }
}
