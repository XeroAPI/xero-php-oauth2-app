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
<html>
    <head>
        <title>My App</title>
    </head>
    <body>
        <a href="index.php">Log in again</a>
    </body>
</html>
