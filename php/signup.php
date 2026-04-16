<?php
session_start();
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Sign Up</title>
  </head>
  <body>
    <div class="container">

    <h2> Create Account </h2>
    <form action="./signup.php" method="POST">
        <label for="fname">First Name:</label><br>
        <input type="text" id="fname" name="fname" required><br><br>
        <label for="lname">Last Name:</label><br>
        <input type="text" id="lname" name="lname" required><br><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <label for="grade">Grade:</label><br>
        <select id="grade" name="grade" required><br><br>
            <option value="" disabled selected>Select Grade</option>
            <option value="Freshman">Freshman</option>
            <option value="Sophomore">Sophomore</option>
            <option value="Junior">Junior</option>
            <option value="Senior">Senior</option>
        </select><br><br>
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
        <button type="submit">Sign Up</button>

        <?php
        require_once "dbFuncs.php";

        if(isset($_POST['fname'], $_POST['lname'], $_POST['email'], $_POST['password'], $_POST['grade'], $_POST['sorority']))
        {
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $grade = $_POST['grade'];
            $sorority = $_POST['sorority'];

            try {
                $pdo = connectDB();

                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
                $checkStmt->execute([$email]);
                $emailExists = $checkStmt->fetchColumn();

                if ($emailExists > 0) {
                    echo "Error: An account with this email already exists.";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO users (fname, lname, email, password, grade, sorority) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$fname, $lname, $email, $password, $grade, $sorority]);
                    echo "Account created successfully!";
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
