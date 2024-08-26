<?php

namespace Facebook\GraphSDK\Exceptions;

use Exception;

/**
 * Custom exception class for handling errors in the Facebook Graph SDK.
 */
class FacebookSDKException extends Exception
{
    /**
     * @var int The error code
     */
    protected $errorCode;

    /**
     * FacebookSDKException constructor.
     *
     * @param string $message The error message
     * @param int $code The error code
     * @param Exception|null $previous The previous exception used for exception chaining
     */
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        $this->errorCode = $code;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns the error code.
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
