<?php
// Start the session
session_start();

// Check if the user is logged in; if not, redirect to login page
if (!isset($_SESSION['username']) || $_SESSION['loggedin'] != true || !isset($_SESSION['user_id'])) {
    header("location:login.php");
    exit;
}
// Include database connection
require "partials/_dbconnect.php";

// Initialize status flags to display alerts based on actions
$insert = false;
$update = false;
$delete = false;

// Check for POST requests, which can be for adding, updating, or deleting notes
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Handle delete
    if (isset($_POST['deleteSno'])) {
        $sno = $_POST['deleteSno'];
        $sql = "DELETE FROM notes WHERE note_id=$sno AND user_id={$_SESSION['user_id']}";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            echo "Error: " . mysqli_error($conn);
        } else {
            $delete = true;
        }
    }

    // Handle update
    elseif (isset($_POST["snoEdit"])) {
        $sno = $_POST["snoEdit"];
        $title = $_POST["titleEdit"];
        $description = $_POST["descriptionEdit"];
        $sql = "UPDATE `notes` SET `title` = '$title', `description` = '$description' WHERE `note_id` = $sno AND user_id={$_SESSION['user_id']}";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            echo "Error: " . mysqli_error($conn);
        } else {
            $update = true;
        }
    }

    // Handle insert
    else {
        $title = $_POST["title"];
        $description = $_POST["description"];
        $sql = "INSERT INTO `notes` (`title`, `description`, `user_id`) VALUES ('$title', '$description', {$_SESSION['user_id']})";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            echo "Error: " . mysqli_error($conn);
        } else {
            $insert = true;
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome <?php echo $_SESSION['username']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
</head>

<body>
    <?php
    // Include navigation bar
    require "partials/nav.php";
    ?>

    <?php
    // Display success alerts for insert, update, or delete actions
    if ($insert) {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            <strong>Success!</strong> You have added a note.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    }
    if ($update) {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            <strong>Success!</strong> You have updated a note.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    }
    if ($delete) {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            <strong>Success!</strong> You have deleted a note.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    }
    ?>
    <!-- Form for adding a new note -->
    <div class="container my-4">
        <h2>Welcome <?php echo $_SESSION['username'] . "<br>"; ?> Add a note to iNotes</h2>
        <form action="/inotes/welcome.php" method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="desc" class="form-label">Description</label>
                <textarea class="form-control" id="desc" name="description" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add note</button>
        </form>
    </div>
    <!-- Table for displaying notes -->
    <div class="container my-4">
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th scope="col">S.no.</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM `notes` WHERE user_id={$_SESSION['user_id']}";
                $result = mysqli_query($conn, $sql);
                $num = mysqli_num_rows($result);
                if ($num > 0) {
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <th scope='row'>$no</th>
                            <td>" . $row['title'] . "</td>
                            <td>" . $row['description'] . "</td>
                            <td>
                                <button class='edit btn btn-sm bg-primary' id=" . $row['note_id'] . ">Edit</button>
                                <button class='delete btn btn-sm bg-danger' id=d" . $row['note_id'] . ">Delete</button>
                            </td>
                        </tr>";
                        $no++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Form for handling deletion, hidden initially -->
    <div class="container">
        <form id="deleteForm" method="post" action="/inotes/welcome.php" style="display: none;">
            <input type="hidden" name="deleteSno" id="deleteSno">
        </form>
    </div>

    <!-- Edit Modal for editing notes -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Note</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/inotes/welcome.php" method="post">
                        <input type="hidden" name="snoEdit" id="snoEdit">
                        <div class="mb-3">
                            <label for="titleEdit" class="form-label">Edit Title</label>
                            <input type="text" class="form-control" id="titleEdit" name="titleEdit">
                        </div>
                        <div class="mb-3">
                            <label for="descEdit" class="form-label">Edit Description</label>
                            <textarea class="form-control" id="descEdit" name="descriptionEdit" rows="3"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update Note</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JavaScript Bundle -->
    <!-- This script loads the JavaScript part of Bootstrap (version 5.3.3) from a CDN. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <!-- jQuery for JavaScript operations -->
    <!-- This loads jQuery (version 3.7.1) from a CDN. -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <!-- DataTables JavaScript for table features -->
    <!-- This script loads DataTables (version 2.1.8) from a CDN. -->
    <script src="//cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

    <script>
        // This initializes a DataTable on an HTML table element with the ID myTable.
        let table = new DataTable('#myTable');

        // Edit note button click event
        edits = document.getElementsByClassName('edit');
        Array.from(edits).forEach((element) => {
            element.addEventListener("click", (e) => {
                const tr = e.target.parentNode.parentNode;
                const title = tr.getElementsByTagName("td")[0].innerText;
                const description = tr.getElementsByTagName("td")[1].innerText;
                const sno = e.target.id;
                document.getElementById('titleEdit').value = title;
                document.getElementById('descEdit').value = description;
                document.getElementById('snoEdit').value = sno;
                //a jQuery code snippet commonly used with Bootstrap modals
                $('#editModal').modal('toggle');
            });
        });
        // Delete note button click event
        deletes = document.getElementsByClassName('delete');
        Array.from(deletes).forEach((element) => {
            element.addEventListener("click", (e) => {
                const sno = e.target.id.substr(1); // Get sno
                if (confirm("Are u sure u want to delete this note?")) {
                    document.getElementById('deleteSno').value = sno;
                    document.getElementById('deleteForm').submit();
                }
            });
        });
    </script>
</body>

</html>