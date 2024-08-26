<?php

require 'vendor/autoload.php';

use Facebook\GraphSDK\BaseClient;
use Facebook\GraphSDK\Response\FacebookResponse;
use Facebook\GraphSDK\Exceptions\FacebookSDKException;

$accessToken = 'user_access_token';

$client = new BaseClient($accessToken);

try {
    $response = $client->get('/me', ['fields' => 'id,name,email']);

    // Create a FacebookResponse object
    $facebookResponse = new FacebookResponse(
        json_encode($response), // Body
        [], // Headers
        200 // Status Code (Assume successful)
    );
    if ($facebookResponse->isSuccess()) {
        print_r($facebookResponse->getDecodedBody());
    } else {
        echo "Request failed with status: " . $facebookResponse->getStatusCode();
    }
} catch (FacebookSDKException $e) {
    echo 'Error: ' . $e->getMessage();
}
