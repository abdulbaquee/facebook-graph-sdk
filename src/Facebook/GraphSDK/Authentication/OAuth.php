<?php

namespace Facebook\GraphSDK\Authentication;

use Facebook\GraphSDK\Exceptions\FacebookSDKException;
use GuzzleHttp\Client;

class OAuth
{
    /**
     * @const string The base authorization URL.
     */
    const BASE_AUTHORIZATION_URL = 'https://www.facebook.com';
    
    /**
     * @const string Production Graph API URL.
     */
    const BASE_GRAPH_URL = 'https://graph.facebook.com';

    /**
     * @const string Default Graph API version for requests.
     */
    const DEFAULT_GRAPH_VERSION = 'v20.0';

    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $graphVersion;
    protected $client;

    /**
     * OAuth constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     */
    public function __construct($clientId, $clientSecret, $redirectUri, $graphVersion = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->graphVersion = $graphVersion ?? static::DEFAULT_GRAPH_VERSION;
        $this->client = new Client([
            'base_uri' => static::BASE_GRAPH_URL,
            'timeout'  => 10.0,
        ]);
    }

    /**
     * Generate the Facebook login URL to initiate the OAuth flow.
     *
     * @param array $scopes
     * @param string $state
     * @return string
     */
    public function getLoginUrl(array $scopes = [], $state = '')
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(',', $scopes),
            'response_type' => 'code',
            'state' => $state,
        ];

        return static::BASE_AUTHORIZATION_URL . '/' . $this->graphVersion . '/dialog/oauth?' . http_build_query($params);
    }

    /**
     * Exchange the authorization code for an access token.
     *
     * @param string $authorizationCode
     * @return array
     * @throws FacebookSDKException
     */
    public function getAccessToken($authorizationCode)
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'client_secret' => $this->clientSecret,
            'code' => $authorizationCode,
        ];

        try {
            $response = $this->client->request('GET', $this->graphVersion . '/oauth/access_token', [
                'query' => $params,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['error'])) {
                throw new FacebookSDKException($data['error']['message'], $data['error']['code']);
            }

            return $data;
        } catch (\Exception $e) {
            throw new FacebookSDKException('Failed to obtain access token: ' . $e->getMessage());
        }
    }

    /**
     * Exchange a short-lived token for a long-lived token.
     *
     * @param string $shortLivedToken
     * @return array
     * @throws FacebookSDKException
     */
    public function getLongLivedToken($shortLivedToken)
    {
        $params = [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'fb_exchange_token' => $shortLivedToken,
        ];

        try {
            $response = $this->client->request('GET', $this->graphVersion . '/oauth/access_token', [
                'query' => $params,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['error'])) {
                throw new FacebookSDKException($data['error']['message'], $data['error']['code']);
            }

            return $data;
        } catch (\Exception $e) {
            throw new FacebookSDKException('Failed to obtain long-lived token: ' . $e->getMessage());
        }
    }
}
