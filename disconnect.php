<html>
	<head>
		<title>My App</title>
	</head>
	<body>
	<?php
		require __DIR__ . '/vendor/autoload.php';

		use XeroPHP\Application\PublicApplication;
		use XeroPHP\Remote\Request;
		use XeroPHP\Remote\URL;
		// Start a session for the oauth session storage
		session_start();

		unset($_SESSION['oauth']['token']);
		unset($_SESSION['oauth']['token_secret']);
		$_SESSION['oauth']['expires'] = null;

		 header("Location: index.php");
	?>
	</body>
</html>
