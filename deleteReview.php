<?php 
session_start();

// Check if the Review_id and title are passed from the URL
if (isset($_GET["Review_id"]) && !empty(trim($_GET["Review_id"]))) {
    $_SESSION["Review_id"] = $_GET["Review_id"];
}

if (isset($_GET["title"]) && !empty(trim($_GET["title"]))) {
    $_SESSION["title"] = $_GET["title"];
}

// Include config file
require_once "config.php";

// Delete the review if confirmed
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION["Review_id"]) && !empty($_SESSION["Review_id"])) {
        $Review_id = $_SESSION['Review_id'];

        // Prepare and execute delete statement
        $sql = "DELETE FROM REVIEW WHERE Review_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_Review_id);
            $param_Review_id = $Review_id;

            if (mysqli_stmt_execute($stmt)) {
                // If successful, redirect to view review page
                header("location: viewReviews.php");
                exit();
            }
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Review</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper {
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
                        <h1>Delete Review</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger fade in">
                            <input type="hidden" name="Review_id" value="<?php echo ($_SESSION["Review_id"]); ?>"/>
                            <input type="hidden" name="title" value="<?php echo ($_SESSION["title"]); ?>"/>
                            <p>Are you sure you want to delete the review for "<?php echo ($_SESSION["title"]); ?>" with ID <?php echo ($_SESSION["Review_id"]); ?>?</p><br>
                            <input type="submit" value="Yes" class="btn btn-danger">
                            <a href="viewReviews.php" class="btn btn-default">No</a>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
