<?php
	session_start();
	// if not logged in, redirect to login page
	if(!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']){
		// to main page since this page inaccessible
		header('Location: login.php');
	}
	
  require 'config/config.php'; // load db credentials

  if ( !isset($_GET['project_id']) || trim($_GET['project_id']) == ''){
    echo '<script>alert("Invalid Project ID.");</script>';
    echo '<script>window.location.href = "home.php";</script>';
  }else {
    // DB Connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if ( $mysqli->connect_errno ) {
			echo $mysqli->connect_error;
			exit();
		}

		$mysqli->set_charset('utf8');

		$project_id = $_GET['project_id'];
		$name = $_GET['name'];

		$sql = "DELETE
						FROM projects
						WHERE project_id = $project_id;";

		$results = $mysqli->query($sql);

		if ( !$results ) {
			echo $mysqli->error;
			$mysqli->close();
			exit();
		}

		// $row = $results->fetch_assoc();

		$mysqli->close();

    echo '<script>alert("' . $name . ' successfully deleted!");</script>';
    echo '<script>window.location.href = "home.php";</script>';
  }

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="A page to process requests for deleting your projects. This page is not directly accessible">
  <link rel="stylesheet" href="shared.css">
	<title>Delete</title>
</head>

</html>