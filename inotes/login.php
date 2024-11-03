<?php
// Initialize a flag to track login errors
$loginError = false;

// Check if the form has been submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Include database connection file
    include 'partials/_dbconnect.php';

    // Retrieve and store the submitted username and password
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database for a user with the provided username
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    // Check if the query failed and display an error if it did
    if (!$result) {
        die("Query failed: " . mysqli_error($conn)); 
    }

    // Check if there is exactly one user with the given username
    $num = mysqli_num_rows($result);
    if ($num == 1) {
        // Fetch the user's data from the database
        $row = mysqli_fetch_assoc($result);
           
        // Verify the submitted password against the hashed password in the database
        if (password_verify($password, $row['password'])) {
            // Start a new session and store user information in session variables
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['id'];

            // Redirect to the welcome page if login is successful
            header("location: welcome.php");
            exit; 
        } else {
            // Set login error message for invalid credentials
            $loginError = "Invalid Credentials.";
        }
    } else {
        // Set login error message if no user or multiple users are found
        $loginError = "Invalid Credentials.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Meta tags for character encoding and viewport settings -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LOG IN</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <?php 
    // Include the navigation bar
    require "partials/nav.php"; 
    ?>

    <?php
    // Display an error alert if login fails
    if ($loginError) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Login Failed! </strong> ' . $loginError . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    }
    ?>

    <div class="container my-4">
        <h1 class="text-center">Login</h1>
        <!-- Login form -->
        <form action="/inotes/login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <!-- Bootstrap JS for interactive elements -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>
