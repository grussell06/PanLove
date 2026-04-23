<?php
session_start();
?>

<!doctype html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="../css/signUp.css">
    <link rel="stylesheet" href="../css/web.css">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Sign Up</title>
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Atma:wght@300;400;500;600;700&family=Original+Surfer&display=swap');
    </style>
  </head>
  <body>
    <div class="signup-container">

    <h2> Create Account </h2>
    <form action="./signup.php" method="POST" enctype="multipart/form-data" class="signup-form">
      <div class="row">
        <div class="col-md-6">
          <label for="fname">First Name:</label><br>
          <input type="text" id="fname" name="fname" required><br><br>
          
          <label for="email">Email:</label><br>
          <input type="email" id="email" name="email" required><br><br>
                
          <label for="grade">Grade:</label><br>
          <select id="grade" name="grade" required><br><br>
            <option value="" disabled selected>Select Grade</option>
            <option value="Freshman">Freshman</option>
            <option value="Sophomore">Sophomore</option>
            <option value="Junior">Junior</option>
            <option value="Senior">Senior</option>
          </select><br><br>

        </div>  
        <div class="col-md-6">
          <label for="lname">Last Name:</label><br>
          <input type="text" id="lname" name="lname" required><br><br>

          <label for="password">Password:</label><br>
          <input type="password" id="password" name="password" required><br><br>  
          
          <label for="sorority">Sorority:</label><br>
          <select id="sorority" name="sorority" required><br><br>
            <option value="" disabled selected>Select Sorority</option>
            <option value="Alpha Delta Pi">Alpha Delta Pi</option>
            <option value="Alpha Gamma Delta">Alpha Gamma Delta</option>
            <option value="Alpha Omicron Pi">Alpha Omicron Pi</option>
            <option value="Delta Gamma">Delta Gamma</option>
            <option value="Delta Zeta">Delta Zeta</option>
            <option value="Kappa Delta">Kappa Delta</option>
            <option value="Phi Mu">Phi Mu</option>
            <option value="Zeta Tau Alpha">Zeta Tau Alpha</option>
          </select><br><br>
        </div>
        <div class="exec-section">
          <label class="group-label">Are you on exec for your sorority?</label>
            <div class="radio-group">
              <div class="radio-item">
                <input type="radio" id="exec_yes" name="is_exec" value="1" required>
                <label for="exec_yes">Yes</label>
              </div>
            <div class="radio-item">
              <input type="radio" id="exec_no" name="is_exec" value="0">
              <label for="exec_no">No</label>
            </div>
          </div>
        </div>
        <div class="profile-upload-section">
          <label for="profile_pic" class="group-label">Profile Picture:</label>
          <input type="file" id="profile_pic" name="profile_pic" accept="image/*" class="form-control">
        </div>

        <div class="form-footer">
            <button type="submit">Sign Up</button>
        </div>

        <?php
        require_once "dbFuncs.php";

        if(isset($_POST['fname'], $_POST['lname'], $_POST['email'], $_POST['password'], $_POST['grade'], $_POST['sorority'], $_POST['is_exec']))
        {
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $grade = $_POST['grade'];
            $sorority = $_POST['sorority'];
            $is_exec = $_POST['is_exec'];
            $profile_pic = "temp_profile.jpg";

            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
              $upload_dir = 'uploads/';
              $file_ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        
              // Secure it by renaming the file to something unique
              $new_filename = uniqid('user_', true) . '.' . $file_ext;
              $target_path = $upload_dir . $new_filename;

              if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
                $profile_pic = $new_filename;
              }
            }

            try {
                $pdo = connectDB();

                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
                $checkStmt->execute([$email]);
                $emailExists = $checkStmt->fetchColumn();

                if ($emailExists > 0) {
                    echo "Error: An account with this email already exists.";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO users (fname, lname, email, password, grade, sorority, is_exec, profile_pic) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$fname, $lname, $email, $password, $grade, $sorority, $is_exec]);
                    //Auto-log in after signing up
                    $_SESSION['user_id'] = $pdo->lastInsertId();
                    $_SESSION['loggedIn'] = true;
                    header("Location: homepage.php");
                }

              } catch (PDOException $e) {
                  echo "Database Error: " . $e->getMessage();
                }
        }
        ?>





    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->

  </div>
  </body>
</html>
