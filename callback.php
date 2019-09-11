<?php
    ini_set('display_errors', 'On');
    require __DIR__ . '/vendor/autoload.php';
    require_once('storage.php');

    // Storage Classe uses sessions for storing token > extend to your DB of choice
    $storage = new StorageClass();  

    $provider = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId'                => '__YOUR_CLIENT_ID__',   
        'clientSecret'            => '__YOUR_CLIENT_SECRET__',
        'redirectUri'             => 'http://localhost:8888/xero-php-oauth2-app/callback.php',
        'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
        'urlAccessToken'          => 'https://identity.xero.com/connect/token',
        'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
    ]);
   
    // If we don't have an authorization code then get one
    if (!isset($_GET['code'])) {
        echo "NO CODE";
        header("Location: index.php?error=true");
        exit();

    // Check given state against previously stored one to mitigate CSRF attack
    } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
        echo "Invalid State";
        unset($_SESSION['oauth2state']);
        exit('Invalid state');
    } else {
        try {
            // Try to get an access token using the authorization code grant.
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);
           
            $config = XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$accessToken->getToken() );
          
            $config->setHost("https://api.xero.com"); 
            $identityInstance = new XeroAPI\XeroPHP\Api\IdentityApi(
                new GuzzleHttp\Client(),
                $config
            );
            
            // Get Array of Tenant Ids
            $result = $identityInstance->getConnections();

            // Save my token, expiration and tenant_id
            $storage->setToken(
                $accessToken->getToken(),
                $accessToken->getExpires(),
                $result[0]->getTenantId(),  
                $accessToken->getRefreshToken());
   
            header('Location: ' . './get.php');
            exit();
     
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            echo "Failed!!!";
            // Failed to get the access token or user details.
            exit($e->getMessage());
        }
    }
?>
    <html>
    <head>
        <title>My App</title>
    </head>
    <body>      
        Opps! Should have redirected to <a href="get.php">to this page</a>
    </body>
</html>
