<?php

namespace Facebook\GraphSDK;

use Facebook\GraphSDK\Exceptions\FacebookSDKException;

class FacebookBatchResponse
{
    protected $responses;
    protected $httpStatusCode;
    protected $rawBody;

    /**
     * FacebookBatchResponse constructor.
     *
     * @param string $rawBody The raw response body
     * @param int $httpStatusCode The HTTP status code of the response
     */
    public function __construct($rawBody, $httpStatusCode)
    {
        $this->rawBody = $rawBody;
        $this->httpStatusCode = $httpStatusCode;

        $this->parseResponse();
    }

    /**
     * Parses the raw response body and sets up the individual responses.
     *
     * @throws FacebookSDKException
     */
    protected function parseResponse()
    {
        $decodedBody = json_decode($this->rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new FacebookSDKException('Failed to decode JSON response: ' . json_last_error_msg());
        }

        if ($this->httpStatusCode >= 400) {
            $error = isset($decodedBody['error']) ? $decodedBody['error'] : ['message' => 'Unknown error', 'code' => $this->httpStatusCode];
            throw new FacebookSDKException($error['message'], $error['code']);
        }

        if (!isset($decodedBody['data']) || !is_array($decodedBody['data'])) {
            throw new FacebookSDKException('Invalid response format: Missing or invalid "data" field');
        }

        $this->responses = $decodedBody['data'];
    }

    /**
     * Returns the raw response body.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->rawBody;
    }

    /**
     * Returns the HTTP status code of the response.
     *
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * Returns all individual responses from the batch.
     *
     * @return array
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * Gets the response for a specific batch part.
     *
     * @param int $index The index of the batch part
     * @return array|null
     */
    public function getResponseByIndex($index)
    {
        return isset($this->responses[$index]) ? $this->responses[$index] : null;
    }

    /**
     * Returns whether the batch request was successful (all individual responses are successful).
     *
     * @return bool
     */
    public function isSuccess()
    {
        foreach ($this->responses as $response) {
            if (isset($response['error'])) {
                return false;
            }
        }
        return true;
    }
}
