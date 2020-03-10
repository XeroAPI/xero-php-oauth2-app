<?php
	ini_set('display_errors', 'On');
	require __DIR__ . '/vendor/autoload.php';
	require_once('storage.php');
	
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();
	$clientId = getenv('CLIENT_ID');
	$clientSecret = getenv('CLIENT_SECRET');
	$redirectUri = getenv('REDIRECT_URI');

	// Storage Classe uses sessions for storing access token > extend to your DB of choice
	$storage = new StorageClass();

	session_start();

	$provider = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId'                => $clientId,   
        'clientSecret'            => $clientSecret,
        'redirectUri'             => $redirectUri,
	    'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
	    'urlAccessToken'          => 'https://identity.xero.com/connect/token',
	    'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
	]);

	// If we don't have an authorization code then get one
	if (!isset($_GET['code'])) {
		$options = [
	    	'scope' => ['openid email profile offline_access assets projects accounting.settings accounting.transactions accounting.contacts accounting.journals.read accounting.reports.read accounting.attachments']
		];

	    // Fetch the authorization URL from the provider; this returns the urlAuthorize option and generates and applies any necessary parameters (e.g. state).
	    $authorizationUrl = $provider->getAuthorizationUrl($options);

	    // Get the state generated for you and store it to the session.
	    $_SESSION['oauth2state'] = $provider->getState();

	    // Redirect the user to the authorization URL.
	    header('Location: ' . $authorizationUrl);
	    exit();

	// Check given state against previously stored one to mitigate CSRF attack
	} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
	    unset($_SESSION['oauth2state']);
	    exit('Invalid state');
	} else {

	}
?>
	<html>
	<head>
		<title>My App</title>
	</head>
	<body>
		Opps! Problem redirecting .....
	</body>
</html>
