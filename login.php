<?php 
	session_start();

	// Is this user already logged in?
	if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
		// User is already logged in. Redirect to homepage.
		header('Location: home.php');
	} else {
		// User is NOT logged in.

		// Was there a form submission?
		if (isset($_POST['name'])) {
      $_SESSION['logged_in'] = true;
      $_SESSION['name'] = $_POST['name'];

      header('Location: home.php');
		}

	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Welcome!</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Enter your name to enter the Crochet Canvas, where you can view and keep track of all your beautiful projects.">
    <style>
      body {
        background-color: #F7F1E9;
        font-family: "Playfair Display", serif;
        display: flex;
        height: 100vh;
        justify-content: center;
        align-items: center;
      }

      button {
        border: none;
        border-radius: 3px 12px 12px 3px;
        padding: 5px 15px;
        height: 50px;
        font-size: 18px;
        font-family: "Playfair Display", serif;
        background-color: #CAB391;
        color: #413221;
      }

      form input {
        width: 300px;
        height: 50px;
        border-radius: 12px 3px 3px 12px;
        border: none;
        background-color: #e3d9cb;
        padding: 5px;
        font-size: 18px;
        color: #413221;
        font-family: "Playfair Display", serif;
        text-align: center;
      }

      /* Mobile - Screen sizes <= 767px */
      @media (max-width: 767px) {
        h3 {
          font-size: 1.4rem;
        }

        button {
          height: 40px;
          font-size: 16px;
          font-family: "Playfair Display", serif;
          background-color: #CAB391;
          color: #413221;
        }

        form input {
          width: 200px;
          height: 40px;
          border-radius: 12px 3px 3px 12px;
          border: none;
          background-color: #e3d9cb;
          padding: 5px;
          font-size: 16px;
          color: #413221;
          font-family: "Playfair Display", serif;
          text-align: center;
        }
      }
    </style>
  </head>
  <body>
    <div style="text-align: center;">
      <h3 style="margin-bottom: 50px;">welcome to crochet canvas :)</h3>
      <form id="name-form" method="POST" onsubmit="return checkForm();" style="margin-bottom: 20px;">
        <input name="name" type="text" id="name" placeholder="type your name">
        <button type="submit" class="submit-button">let's go!</button>
      </form>
      <p id="name-error" class="text-danger"></p>
    </div>
    <script>
    function checkForm() {
      if(document.querySelector("#name").value.trim() == ""){
        document.querySelector("#name-error").innerHTML = "fill out your name!"
        return false;
      }else{
        document.querySelector("#name").innerHTML = "";
        return true;
      }
    }
  </script>
  </body>
</html>