<?php
// Initialize variables to manage alerts for successful signup and errors
$showAlert = false;
$showError = false;

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Include the database connection file
    include 'partials/_dbconnect.php';

    // Get the username and password values from the form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Check if the username already exists in the database
    $existSql = "SELECT * FROM users WHERE username ='$username'";
    $resultExist = mysqli_query($conn, $existSql);
    if (!$resultExist) {
        // Stop execution if the query fails and display the error
        die("Query failed: " . mysqli_error($conn)); 
    }
    
    // Count the number of rows that match the username
    $numExist = mysqli_num_rows($resultExist);
    if ($numExist > 0) {
        // Set an error message if the username already exists
        $showError = "Username already exists";
    } else {
        // Check if the passwords match
        if ($password === $cpassword) {
            // Hash the password before storing it in the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            // Insert the new user into the database with the hashed password
            $sql = "INSERT INTO `users` (`username`, `password`) VALUES ('$username', '$hashedPassword')";
            $result = mysqli_query($conn, $sql);
            if (!$result) {
                // Stop execution if the query fails and display the error
                die("Query failed: " . mysqli_error($conn));
            }
            // Set success alert if signup is successful
            $showAlert = true;
        } else {
            // Set an error message if passwords do not match
            $showError = "Passwords do not match";
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <?php require "partials/nav.php"; ?>

    <?php
    // Display success alert if signup was successful
    if ($showAlert) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Signup Successful! </strong> You can now login.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
    // Display error alert if there was a signup error
    if ($showError) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Signup Failed! </strong> ' . $showError . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
    ?>

    <div class="container my-4">
        <h1 class="text-center">Sign Up</h1>
        <form action="/inotes/signup.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="cpassword" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="cpassword" name="cpassword" required>
                <div id="passhelp" class="form-text">Make sure you enter the same password</div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
