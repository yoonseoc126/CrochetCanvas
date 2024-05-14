<?php 
	session_start();

	// Is this user already logged in?
	if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    // then LOG OUT
    session_destroy();

    // show alert, then redirect after user clicks "ok"
    echo '<script>window.location.href = "login.php";</script>';
	} else {
		// User is NOT logged in.

    // then why are you here. redirect.
    header('Location: login.php');


	}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Loading...</title>
    <link rel="stylesheet" href="shared.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Page to process logout request. This page cannot be directly accessed, and will redirect you to the login page.">
  </head>
  <body>
    <p>Logging you out...</p>
  </body>
</html>