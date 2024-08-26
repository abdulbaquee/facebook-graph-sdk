<?php

namespace Facebook\GraphSDK\Utils;

use DateTime;
use DateTimeZone;
use Facebook\GraphSDK\Exceptions\FacebookSDKException;

class GraphHelper
{
    /**
     * Formats the given timestamp to a DateTime object.
     *
     * @param string $timestamp
     * @param string $timezone
     * @return DateTime
     * @throws FacebookSDKException
     */
    public static function formatTimestamp($timestamp, $timezone = 'UTC')
    {
        try {
            $dateTime = new DateTime("@$timestamp", new DateTimeZone($timezone));
            $dateTime->setTimezone(new DateTimeZone($timezone));
            return $dateTime;
        } catch (\Exception $e) {
            throw new FacebookSDKException('Failed to format timestamp: ' . $e->getMessage());
        }
    }

    /**
     * Builds a query string from an associative array of parameters.
     *
     * @param array $params
     * @return string
     */
    public static function buildQueryString(array $params)
    {
        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Extracts the access token from a query string.
     *
     * @param string $queryString
     * @return string|null
     */
    public static function extractAccessToken($queryString)
    {
        parse_str(parse_url($queryString, PHP_URL_QUERY), $params);
        return isset($params['access_token']) ? $params['access_token'] : null;
    }

    /**
     * Validates a Facebook API response by checking for common error patterns.
     *
     * @param array $response
     * @throws FacebookSDKException
     */
    public static function validateApiResponse(array $response)
    {
        if (isset($response['error'])) {
            $message = isset($response['error']['message']) ? $response['error']['message'] : 'Unknown error';
            $code = isset($response['error']['code']) ? $response['error']['code'] : 0;

            throw new FacebookSDKException($message, $code);
        }
    }

    /**
     * Formats a Facebook Graph API error message for display.
     *
     * @param array $error
     * @return string
     */
    public static function formatErrorMessage(array $error)
    {
        $message = isset($error['message']) ? $error['message'] : 'Unknown error';
        $code = isset($error['code']) ? $error['code'] : 0;
        $type = isset($error['type']) ? $error['type'] : 'Unknown type';

        return "Error [$code - $type]: $message";
    }
}
