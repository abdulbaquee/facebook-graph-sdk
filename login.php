<?php

require 'vendor/autoload.php';

use Facebook\GraphSDK\Authentication\OAuth;

$client_id = 'your_app_id';

$client_secret = 'your_app_secret';

$callback_uri = 'your_call_back_url';

$graph_version = 'v20.0'; // optional

$permissions = ['email', 'public_profile'];

$oauth = new OAuth($client_id, $client_secret, $callback_uri, $graph_version);

// Step 1: Redirect user to Facebook login URL
$loginUrl = $oauth->getLoginUrl($permissions);

echo '<a href="' . $loginUrl . '">Login with Facebook</a>';

// Step 2: Handle Facebook's redirect back to your site
if (isset($_GET['code'])) {
    $accessTokenData = $oauth->getAccessToken($_GET['code']);
    $accessToken = $accessTokenData['access_token'];

    // Optionally exchange for a long-lived token
    $longLivedTokenData = $oauth->getLongLivedToken($accessToken);
    $longLivedToken = $longLivedTokenData['access_token'];

    // Use the access token to make API requests
    echo 'Access Token: ' . $longLivedToken;
}
