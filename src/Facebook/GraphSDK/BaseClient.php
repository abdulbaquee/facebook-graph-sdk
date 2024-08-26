<?php

namespace Facebook\GraphSDK;

use Facebook\GraphSDK\Exceptions\FacebookSDKException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class BaseClient
{
    /**
     * @const string Production Graph API URL.
     */
    const BASE_GRAPH_URL = 'https://graph.facebook.com';

    /**
     * @const string Default Graph API version for requests.
     */
    const DEFAULT_GRAPH_VERSION = 'v20.0';

    protected $accessToken;
    protected $client;
    protected $graphVersion;

    /**
     * BaseClient constructor.
     *
     * @param string $accessToken
     * @param string $graphVersion
     */
    public function __construct($accessToken, $graphVersion = null)
    {
        $this->accessToken = $accessToken;
        $this->graphVersion = $graphVersion ?? static::DEFAULT_GRAPH_VERSION;
        $this->client = new Client([
            'base_uri' => static::BASE_GRAPH_URL . "/" . $this->graphVersion . "/",
            'timeout'  => 10.0,
        ]);
    }

    /**
     * Send a GET request to the Facebook Graph API.
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     * @throws FacebookSDKException
     */
    public function get($endpoint, array $params = [])
    {
        return $this->sendRequest('GET', $endpoint, $params);
    }

    /**
     * Send a POST request to the Facebook Graph API.
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     * @throws FacebookSDKException
     */
    public function post($endpoint, array $params = [])
    {
        return $this->sendRequest('POST', $endpoint, $params);
    }

    /**
     * Send a DELETE request to the Facebook Graph API.
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     * @throws FacebookSDKException
     */
    public function delete($endpoint, array $params = [])
    {
        return $this->sendRequest('DELETE', $endpoint, $params);
    }

    /**
     * Send an HTTP request to the Facebook Graph API.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $params
     * @return array
     * @throws FacebookSDKException
     */
    protected function sendRequest($method, $endpoint, array $params = [])
    {
        $params['access_token'] = $this->accessToken;

        try {
            $response = $this->client->request($method, $endpoint, [
                'query' => $method === 'GET' ? $params : [],
                'form_params' => $method !== 'GET' ? $params : [],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['error'])) {
                throw new FacebookSDKException($data['error']['message'], $data['error']['code']);
            }

            return $data;
        } catch (RequestException $e) {
            throw new FacebookSDKException('Request failed: ' . $e->getMessage(), $e->getCode());
        }
    }
}
