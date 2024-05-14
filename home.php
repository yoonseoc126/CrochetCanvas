<?php 
  session_start();
  // if not logged in, redirect to login page
  if(!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']){
    // to main page since this page inaccessible
    header('Location: login.php');
  }

  require 'config/config.php'; // load db credentials

  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	// check for connection errors
	if ( $mysqli->connect_errno ) {
		echo $mysqli->connect_error;
		exit();
	}

  $mysqli->set_charset('utf8');

  // add any new data to database
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $mysqli->escape_string($_POST['name']);

    $date = $mysqli->escape_string($_POST['date']);

    $status = $_POST['status'];

    $duration = $mysqli->escape_string($_POST['duration']);

    if($_POST['yarn'] == null){
      $yarn = null;
    }else {
      $yarn = $_POST['yarn'];
    }


    if(($_FILES['image']['name']) == ""){
      $image = null;
    }else if($_FILES['image']['error'] && $_FILES['image']['error'] != 4){
      $image = null;
      echo '<script>alert("Image upload error.");</script>';
    }else{
      $fileName = $_FILES['image']['name'];
      // move uploaded image to 'img' folder
      $temp_loc = $_FILES['image']['tmp_name'];
      $dest = "img/" . $_FILES['image']['name'];
      move_uploaded_file($temp_loc, $dest);

      $image_sql = "INSERT INTO images (path) VALUES ('$fileName');";
      $result = $mysqli->query($image_sql);
      if(!$result){
        echo $mysqli->error;
        $mysqli->close();
        exit();
      }
      $image = $mysqli->insert_id;
    }



    $hook_size = $mysqli->escape_string($_POST['hook-size']);
    $url = $mysqli->escape_string($_POST['url']);
    $notes = null; // add notes section later

    // check if entry exists by checking same name/date
    $checkSQL = "SELECT * FROM projects WHERE name = ? AND date = ?";
    $checkStmt = $mysqli->prepare($checkSQL);
    $checkStmt->bind_param("ss", $name, $date);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();


    if($checkResult->num_rows == 0){ // if does not exist
      $sql = "INSERT INTO projects (name, date, status, duration, yarn_id, hook_size, image_id, url, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = $mysqli->prepare($sql);
  
      // assign values to parameters
      $stmt->bind_param("ssisisiss", $name, $date, $status, $duration, $yarn, $hook_size, $image, $url, $notes);
  
      if ($stmt->execute()) {
          echo '<script>alert("new entry created :)");</script>';
      } else {
          echo "Error: " . $sql . "<br>" . $stmt->error;
      }
  
      $stmt->close();

      // use javascript here to wait until ok is clicked on the alert
      echo '<script>window.location.href = "home.php";</script>';
    }
  }




  // get projects from database
  $sql = "SELECT project_id, projects.name, projects.date, images.path AS image, status.name AS status
  FROM projects
  LEFT JOIN yarns
    ON projects.yarn_id = yarns.yarn_id
  LEFT JOIN images
    ON projects.image_id = images.image_id
  LEFT JOIN status
    ON projects.status = status.status_id";

  $results = $mysqli->query($sql);
  $num_results = $results->num_rows;

  // ADD COUNT() for # not started, # in progress, etc.

  if ( !$results ) {
		echo $mysqli->error;
		$mysqli->close();
		exit();
	}

  $yarn_sql = "SELECT * FROM yarns";

  $yarn_names = $mysqli->query($yarn_sql);

  if ( !$yarn_names ) {
		echo $mysqli->error;
		$mysqli->close();
		exit();
	}

  $result1 = $mysqli->query("SELECT COUNT(*) AS count FROM projects WHERE status='1';");
  $result2 = $mysqli->query("SELECT COUNT(*) AS count FROM projects WHERE status='2';");
  $result3 = $mysqli->query("SELECT COUNT(*) AS count FROM projects WHERE status='3';");

  if ( !$result1 || !$result2 || !$result3 ) {
		echo $mysqli->error;
		$mysqli->close();
		exit();
	}

  $status_none = $result1->fetch_assoc();
  $status_progress = $result2->fetch_assoc();
  $status_finished = $result3->fetch_assoc();

  $percent_none = round(($status_none['count'])/$num_results, 2); // round to two decimal points
  $percent_progress = round(($status_progress['count'])/$num_results, 2);
  $percent_finished = round(($status_finished['count'])/$num_results, 2);

  // close connection
  $mysqli->close();
  
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <Title>Crochet Canvas</Title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">    <link rel="stylesheet" href="shared.css">
    <link rel="stylesheet" href="home.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Never forget about your crochet project again! Track your crochet projects with Crochet Canvas's detailed database.">
  </head>
  <body>

    <nav class="navbar navbar-expand align-items-bottom">
      <a class="navbar-brand" href="home.php"><em>crochet canvas</em></a>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link text-nowrap" href="home.php">home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-nowrap" href="#projects-header">my projects</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-nowrap" href="logout.php">logout</a>
          </li>
        </ul>
      </div>
    </nav>
    <div id="page-wrapper">
      <h3>Hi, <?php echo $_SESSION['name']; ?>!</h3>
      <small>click on a project name for more details.</small>
      <div id="gallery">
        <div class="gallery-img-container"><img src="img/capybara.png" alt=""></div>
        <div class="gallery-img-container"><img src="img/crochet_cat.png" alt=""></div>
        <div class="gallery-img-container"><img src="img/crochet_duck.png" alt=""></div>
        <div class="gallery-img-container"><img src="img/mushroom_crochet.png" alt=""></div>
      </div>
      <div id="projects-wrapper">
        <div id="header-wrapper">
          <div id="projects-header" class="d-flex align-items-center">
            <p class="align-middle">my projects</p>
            <button class="btn btn-light" id="new-button" onclick="showOverlay()">+ new</button>
          </div>
          <div id="badges" class="d-flex align-items-center">
            <span class="badge status-none" style="font-weight: 300;"><span style="font-weight: 700;"><?php echo $percent_none; ?>%</span> not started</span>
            <span class="badge status-progress ml-2" style="font-weight: 300;"><span style="font-weight: 700;"><?php echo $percent_progress; ?>%</span> in progress</span>
            <span class="badge status-finished ml-2" style="font-weight: 300;"><span style="font-weight: 700;"><?php echo $percent_finished; ?>%</span> finished</span>
          </div>
        </div>

        <table class="table table-hover">
          <tbody>

          <?php while ( $row = $results->fetch_assoc() ) : ?>
            <tr>
              <td class="col-2 align-middle"><img class="project-img" src=<?php 
                if(!$row['image']){ // if image is not set in database
                  echo "img/crochet.jpeg";
                }else{
                  echo "img/" . $row['image'];
                }
              ?> alt=""></td>

              <td class="col-3 align-middle name-output"><a href="details.php?project_id=<?php echo $row['project_id']?>" class="link-secondary"><?php echo $row['name']?></a></td>
              <td class="col-3 align-middle date-output"><?php echo $row['date']?></td>
              <td class="col-3 align-middle status-output"><span class="badge <?php 
                if($row['status'] == 'in progress'){
                  echo "status-progress";
                }else if($row['status'] == 'not started'){
                  echo "status-none";
                }else{
                  echo "status-finished";
                }
              ?>"><?php echo $row['status']?></span></td>
              <td class="col-1 align-middle"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16" style="cursor: pointer;" onclick="deleteProject(<?php echo $row['project_id']; ?>, '<?php echo $row['name']; ?>')">
                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
              </svg></td>
            </tr>
          <?php endwhile; ?>

          </tbody>
        </table>
      </div> <!--#projects-wrapper-->
      <div class="overlay hidden" id="newpost-overlay">
        <div class="overlay-content">
          <button id="close-button" onclick="hideOverlay()"><i class="bi bi-x"></i></button>
          <div class="overlay-inner-content">
            <h3>New Post</h3>
            <form id="newpost-form" method="POST" enctype="multipart/form-data" onsubmit="return checkURL()">
              <div class="form-group">
                <label for="name">name: </label>
                <input name="name" type="text" id="name" required><span class="text-danger">*</span>
              </div>

              <div class="form-group">
                <label for="date">date: </label>
                <input name="date" type="date" id="date" required><span class="text-danger">*</span>
              </div>

              <div class="form-group">
                <label for="status">status: </label>
                <select name="status" id="status" required>
                  <option value="">select...</option>
                  <option value="1">not started</option>
                  <option value="2">in progress</option>
                  <option value="3">finished</option>
                </select><span class="text-danger">*</span>
              </div>

              <div class="form-group">
                <label for="duration">duration: </label>
                <input name="duration" type="text" id="duration">
              </div>

              <div class="form-group">
                <label for="yarn">yarn: </label>
                <select name="yarn" id="yarn">
                <option value="">select...</option>
                <?php while ( $row = $yarn_names->fetch_assoc() ) : ?>
                  <option value=<?php echo $row['yarn_id']?>><?php echo $row['name']?></option>
                <?php endwhile; ?>
                </select>
              </div>

              <div class="form-group">
                <label for="hook-size">hook size: </label>
                <input name="hook-size" type="text" id="hook-size">
              </div>

              <div class="form-group">
                <label for="image">image file: </label>
                <input id="image" name="image" type="file" accept="image/*" >
              </div>

              <div class="form-group">
                <label for="url">url: </label>
                <input name="url" type="url" id="url">
              </div>

              <small id="form-error" class="text-danger"></small>

              <button type="submit" class="submit-button">add!</button>

            </form>
          </div>
      </div>
      </div>

    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="home.js"></script>
    <script>
      // ONCLICK DELETE HANDLERS

      function deleteProject(projectID, projectName){
        if (confirm('Are you sure you want to delete this project?')) {
              window.location.href = `delete.php?project_id=${projectID}&name=${projectName}`;
          }
      }

      function checkURL(){
        const url = document.querySelector("#url").value.trim()

        if(!url || url.includes("https://www.youtube.com/watch?v=") || url.includes("https://youtu.be/")){ // if empty, just submit form
          document.querySelector("#form-error").innerHTML = ""
          return true;
        }else{
          // not correct url
          document.querySelector("#form-error").innerHTML = "Please enter a youtube URL"
          return false;
        }
      }


    </script>
  </body>

</html>