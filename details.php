<?php 

  session_start();
  // if not logged in, redirect to login page
  if(!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']){
    // to main page since this page inaccessible
    header('Location: login.php');
  }

  require 'config/config.php'; // load db credentials


  if(!isset($_GET['project_id'])){
    header('Location: home.php');
  }
  $project_id = $_GET['project_id'];
  

  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	// check for connection errors
	if ( $mysqli->connect_errno ) {
		echo $mysqli->connect_error;
		exit();
	}

  $mysqli->set_charset('utf8');


  $sql = "SELECT project_id, projects.name, projects.date, projects.duration, yarns.name AS yarn, projects.hook_size, images.path AS image, images.image_id, projects.url, status.name AS status
  FROM projects
  LEFT JOIN yarns
    ON projects.yarn_id = yarns.yarn_id
  LEFT JOIN images
    ON projects.image_id = images.image_id
  LEFT JOIN status
    ON projects.status = status.status_id
  WHERE project_id='" . $project_id . "';";

  $result = $mysqli->query($sql)->fetch_assoc();

  // ADD COUNT() for # not started, # in progress, etc.

  if ( !$result ) {
    echo $mysqli->error;
    $mysqli->close();
    exit();
  }

  // get yarn names
  $yarn_sql = "SELECT * FROM yarns";

  $yarn_names = $mysqli->query($yarn_sql);

  if ( !$yarn_names ) {
		echo $mysqli->error;
		$mysqli->close();
		exit();
	}

  // PROCESS FORM REQUEST
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


    if(($_FILES['image']['name']) == ""){ // we just use the original file in db
      $image = $result['image_id'];
    }else if($_FILES['image']['error'] && $_FILES['image']['error'] != 4){
      $image = null;
      echo '<script>alert("Image upload error #' . $_FILES['image']['error'] . '.");</script>';
    }else{
      $fileName = $_FILES['image']['name'];
      // move uploaded image to 'img' folder
      $temp_loc = $_FILES['image']['tmp_name'];
      $dest = "img/" . $_FILES['image']['name'];
      
      move_uploaded_file($temp_loc, $dest);

      $image_sql = "INSERT INTO images (path) VALUES ('$fileName');";
      $img_result = $mysqli->query($image_sql);
      if(!$img_result){
        echo $mysqli->error;
        $mysqli->close();
        exit();
      }
      $image = $mysqli->insert_id;
    }




    $hook_size = $mysqli->escape_string($_POST['hook-size']);
    $url = $mysqli->escape_string($_POST['url']);
    $notes = null;

    $updateSQL = "UPDATE projects SET name=?, date=?, status=?, duration=?, yarn_id=?, hook_size=?, image_id=?, url=?, notes=? WHERE project_id=?";
    $updateStmt = $mysqli->prepare($updateSQL);

    // Assign values to parameters for the update statement
    $updateStmt->bind_param("ssisisissi", $name, $date, $status, $duration, $yarn, $hook_size, $image, $url, $notes, $project_id);

    if ($updateStmt->execute()) {
      // do nothing
    } else {
        echo "Error updating entry: " . $updateStmt->error;
    }

    $updateStmt->close();

    // use javascript here to wait until ok is clicked on the alert
    echo '<script>window.location.href = "details.php?project_id=' . $project_id . '";</script>';
  }


  // close connection
  $mysqli->close();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <Title>Details</Title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Lora:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="shared.css">
    <link rel="stylesheet" href="details.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Check out all the details of each crochet project, with images and youtube tutorial links, and edit your projects with new details.">
  </head>
  <body>
    <div class="overall-wrapper">
      <div class="img-container">
        <img class="project-img" src="img/<?php 
          if($result['image'] == null){
            echo "crochet.jpeg";
          }else{
            echo $result['image']; 
          }
        ?>" alt="">
      </div>
      <div class="detail-wrapper">
        <h3 id="name-output"><?php echo $result['name']; ?></h3>
        <div id="date-output"><strong>start date:</strong> <?php echo $result['date']; ?></div>
        <div id="status-output"><strong>status:</strong> <?php echo $result['status']; ?></div>
        <div id="duration-output"><strong>duration:</strong> <?php echo $result['duration']; ?></div>
        <div id="yarn-output"><strong>yarn:</strong> <?php echo $result['yarn']; ?> </div>
        <div id="hook-output"><strong>hook size:</strong> <?php echo $result['hook_size']; ?></div>
      </div>
    </div>
    <div id="buttons-wrapper">
      <a href="home.php" id="back-button" class="btn btn-light">back to projects</a>
      <button id="edit-button" class="btn btn-light" onclick="showOverlay()">edit</button>
    </div>
    <?php if(!empty($result['url'])) : ?>
      <iframe id="player" width="640" height="390" src=URL('<?php $result['url'] ?>')></iframe>
    <?php endif; ?>
    <div class="overlay hidden" id="newpost-overlay">
        <div class="overlay-content">
          <button id="close-button" onclick="hideOverlay()"><i class="bi bi-x"></i></button>
          <div class="overlay-inner-content">
            <h3>Edit Details</h3>

            <form id="edit-form" method="POST" enctype="multipart/form-data">
              <div class="form-group">
                <label for="name">name: </label>
                <input id="name" name="name" type="text" value="<?php echo $result['name']; ?>" required><span class="text-danger">*</span>
              </div>

              <div class="form-group">
                <label for="date">date: </label>
                <input id="date" name="date" type="date" value=<?php echo $result['date']; ?> required><span class="text-danger">*</span>
              </div>

              <div class="form-group">
                <label for="status">status: </label>
                <select name="status" id="status">
                  <?php if($result['status'] == "not started") :?>
                    <option selected value="1">not started</option>
                    <option value="2">in progress</option>
                    <option value="3">finished</option>
                  <?php elseif($result['status'] == "in progress") : ?>
                    <option value="1">not started</option>
                    <option selected value="2">in progress</option>
                    <option value="3">finished</option>
                  <?php else : ?>
                    <option value="1">not started</option>
                    <option value="2">in progress</option>
                    <option selected value="3">finished</option>
                  <?php endif; ?>
                  
                </select><span class="text-danger">*</span>
              </div>

              <div class="form-group">
                <label for="duration">duration: </label>
                <input id="duration" name="duration" type="text" value="<?php echo $result['duration']; ?>">
              </div>

              <div class="form-group">
                <label for="yarn">yarn: </label>
                <select name="yarn" id="yarn">
                    <option value="">select...</option>
                    <?php while ($row = $yarn_names->fetch_assoc()) : ?>
                        <?php if ($row['name'] == $result['yarn']) : ?>
                            <option value="<?php echo $row['yarn_id'] ?>" selected><?php echo $row['name'] ?></option>
                        <?php else : ?>
                            <option value="<?php echo $row['yarn_id'] ?>"><?php echo $row['name'] ?></option>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </select>             
              </div>

              <div class="form-group">
                <label for="hook-size">hook size: </label>
                <input id="hook-size" name="hook-size" type="text" value="<?php echo $result['hook_size']; ?>">
              </div>

              <div class="form-group">
                <label for="image">image file: </label>
                <input id="image" name="image" type="file" accept="image/*">
              </div>

              <div class="form-group">
                <label for="url">url: </label>
                <input id="url" name="url" type="url" value=<?php if(!empty($result['url'])){ echo $result['url']; }?>>
              </div>
              

              <small id="form-error" class="text-danger"></small>

              <button type="submit" class="submit-button">done!</button>

            </form>
          </div>
      </div>
    </div>


    <script src="home.js"></script>
    <script>
      function getURL(url){
        let videoID = ""

        console.log(url)

        if(url.includes("https://www.youtube.com/watch?v=")){
          // regex to get video id from url (after "v=" and before "&")
          const regex = /v=([^&]*)/;

          // find videoID in URL
          const match = url.match(regex);

          // get videoID and add to URL
          if (match && match[1]) {
              videoId = match[1];
              console.log("Video ID:", videoId);
          }
        }else if( url.includes("https://youtu.be/")){
          const regex = /youtu\.be\/([^?]+)/;

          // Use the regex pattern to extract the video ID
          const match = url.match(regex);

          // get videoID and add to URL
          if (match && match[1]) {
              videoId = match[1];
              console.log("Video ID:", videoId);
          }
        }

        return "http://www.youtube.com/embed/" + videoId + "?enablejsapi=1&origin=http://example.com"

      }

      document.getElementById('player').src = getURL('<?php echo $result['url']; ?>');




    </script>
  </body>
</html>