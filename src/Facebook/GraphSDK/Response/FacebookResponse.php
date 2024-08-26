<?php

namespace Facebook\GraphSDK\Response;

use Facebook\GraphSDK\Exceptions\FacebookSDKException;

class FacebookResponse
{
    protected $body;
    protected $headers;
    protected $statusCode;

    /**
     * FacebookResponse constructor.
     *
     * @param string $body
     * @param array $headers
     * @param int $statusCode
     */
    public function __construct($body, array $headers, $statusCode)
    {
        $this->body = $body;
        $this->headers = $headers;
        $this->statusCode = $statusCode;

        $this->validateResponse();
    }

    /**
     * Validates the response to ensure no errors occurred.
     *
     * @throws FacebookSDKException
     */
    protected function validateResponse()
    {
        $decodedBody = $this->getDecodedBody();

        if ($this->statusCode >= 400) {
            $message = isset($decodedBody['error']['message']) ? $decodedBody['error']['message'] : 'Unknown error';
            $code = isset($decodedBody['error']['code']) ? $decodedBody['error']['code'] : $this->statusCode;

            throw new FacebookSDKException($message, $code);
        }
    }

    /**
     * Returns the raw response body as a string.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Returns the decoded response body as an associative array.
     *
     * @return array
     * @throws FacebookSDKException
     */
    public function getDecodedBody()
    {
        $decodedBody = json_decode($this->body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new FacebookSDKException('Failed to decode JSON response: ' . json_last_error_msg());
        }

        return $decodedBody;
    }

    /**
     * Returns the HTTP headers from the response.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns the HTTP status code from the response.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Returns whether the response was successful (status code 2xx).
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Returns whether the response was a client error (status code 4xx).
     *
     * @return bool
     */
    public function isClientError()
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Returns whether the response was a server error (status code 5xx).
     *
     * @return bool
     */
    public function isServerError()
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }
}
