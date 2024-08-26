# Unofficial Facebook Graph SDK

It is a PHP SDK for seamless interaction with the Facebook Graph API. It offers an intuitive interface for accessing user profiles, pages, ads, and more. Perfect for developers, it simplifies authentication, data handling, and API requests, making Facebook integration easy for PHP projects.

Simplified version of Facebook Graph SDK

Version: 1.0.0

Website: [webgrapple.com](http://www.webgrapple.com/)

Author: [abdulbaquee](http://www.twitter.com/abdulbaquee85)

# Usage of Unofficial Facebook Graph SDK

This application requires the Google My Business API v4.0

# 1. Installation
First, install the SDK via Composer:

```
composer require abdulbaquee/facebook-graph-sdk
```

# 2. Basic Setup
Start by including the autoload file and initializing the SDK:

```
require 'vendor/autoload.php';

use Facebook\GraphSDK\OAuth;
use Facebook\GraphSDK\BaseClient;

$oauth = new OAuth('your-app-id', 'your-app-secret', 'your-redirect-uri', 'graph-version');
```

# 3. Authentication
Redirect the user to Facebook's login page to get an authorization code:
```
$loginUrl = $oauth->getLoginUrl(['email', 'public_profile']);
header('Location: ' . $loginUrl);
exit;
```
After the user authorizes, handle the callback to obtain an access token:
```
if (isset($_GET['code'])) {
    $accessToken = $oauth->getAccessTokenFromCode($_GET['code']);
    echo 'Access Token: ' . $accessToken;
}
```

# 4. Making API Requests
Use the BaseClient to make requests to the Facebook Graph API:
```
$client = new BaseClient($accessToken);
$response = $client->get('/me?fields=id,name,email');
$user = $response->getBody();

echo 'ID: ' . $user['id'];
echo 'Name: ' . $user['name'];
echo 'Email: ' . $user['email'];
```

# 5. Batch Requests
For multiple requests in a single call:
```
$batch = [
    $client->createRequest('GET', '/me?fields=id,name'),
    $client->createRequest('GET', '/me/friends'),
];

$batchResponse = $client->sendBatchRequest($batch);

foreach ($batchResponse->getResponses() as $response) {
    print_r($response->getBody());
}

```

# 6. Error Handling
Handle errors gracefully using exceptions:
```
try {
    $response = $client->get('/me?fields=id,name');
} catch (FacebookSDKException $e) {
    echo 'Error: ' . $e->getMessage();
}
```

# Requirements
To use the Facebook Graph SDK for PHP, ensure your environment meets the following requirements:

1. `PHP Version`: PHP 7.4 or higher
2. `Composer`: Installed for dependency management
3. `cURL` Extension: Enabled in your PHP environment
3. `SSL/TLS`: Enabled for secure API communication
4. `Facebook App`: You must have a Facebook App with a valid App ID and App Secret

# Important Links
1. Facebook Developer Documentation: [https://developers.facebook.com/docs/graph-api]
2. Facebook App Dashboard: [https://developers.facebook.com/apps]
3. Composer Installation: [https://getcomposer.org/download/]
4. PHP cURL Extension: [https://www.php.net/manual/en/book.curl.php]
5. GitHub Repository: [https://github.com/abdulbaquee/facebook-graph-sdk]