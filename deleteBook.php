<?php
session_start();
if (isset($_GET["Book_id"]) && !empty(trim($_GET["Book_id"]))) {
    $_SESSION["Book_id"] = $_GET["Book_id"];
}

if (isset($_GET["title"]) && !empty(trim($_GET["title"]))) {
    $_SESSION["title"] = $_GET["title"];
}

// Include config file
require_once "config.php";


// Delete a book based on id after confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION["Book_id"]) && !empty($_SESSION["Book_id"])) {
        $Book_id = $_SESSION['Book_id'];

        // Prepare a delete statement
        $sql = "DELETE FROM BOOK WHERE Book_id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_Book_id);

            // Set parameters
            $param_Book_id = $Book_id;

            // Execute DELETE
            if (mysqli_stmt_execute($stmt)) {
                // If successful, redirect to index page
                header("location: index.php");
                exit();
            } else {
                echo "Error deleting the book. Error: " . mysqli_error($link);
            }
        }
    }
    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($link);
} else {
    // Check existence of id parameter
    if (empty(trim($_GET["Book_id"]))) {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Book</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>Delete Book</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger fade in">
                            <input type="hidden" name="Book_id" value="<?php echo ($_SESSION["Book_id"]); ?>"/>
                            <input type="hidden" name="Title" value="<?php echo ($_SESSION["title"]); ?>"/>
                            <p>Are you sure you want to delete the book titled "<?php echo ($_SESSION["title"]); ?>" with ID <?php echo ($_SESSION["Book_id"]); ?>?</p><br>
                            <input type="submit" value="Yes" class="btn btn-danger">
                            <a href="index.php" class="btn btn-default">No</a>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
