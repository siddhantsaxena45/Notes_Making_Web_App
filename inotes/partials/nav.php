<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Set the logged status 
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  $logged = true;
} else {
  $logged = false;
}

echo '
<nav class="navbar navbar-expand-lg bg-dark navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">iNotes</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="/inotes/welcome.php">Home</a>
        </li>
';

if ($logged) {
  echo '
        <li class="nav-item">
          <a class="nav-link" href="/inotes/logout.php">Logout</a>
        </li>';
} else {
  echo '
        <li class="nav-item">
          <a class="nav-link" href="/inotes/login.php">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/inotes/signup.php">Signup</a>
        </li>';
}

echo '</ul>
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>
';

?>