<?php
	session_start();
	if(isset($_GET["Member_id"]) && !empty(trim($_GET["Member_id"]))){
		$_SESSION["Member_id"] = $_GET["Member_id"];
	}

    require_once "config.php";
	// Delete an Employee's record after confirmation
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_SESSION["Member_id"]) && !empty($_SESSION["Member_id"])){ 
			$Member_id = $_SESSION['Member_id'];
			// Prepare a delete statement
			$sql = "DELETE FROM MEMBER WHERE Member_id = ?";
   
			if($stmt = mysqli_prepare($link, $sql)){
			// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "s", $param_Member_id);
 
				// Set parameters
				$param_Member_id = $Member_id;
       
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Records deleted successfully. Redirect to landing page
					header("location: index.php");
					exit();
				} else{
					echo "Error deleting the member";
				}
			}
		}
		// Close statement
		mysqli_stmt_close($stmt);
    
		// Close connection
		mysqli_close($link);
	} else{
		// Check existence of id parameter
		if(empty(trim($_GET["Member_id"]))){
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
    <title>View Record</title>
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
                        <h1>Delete Record</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger fade in">
                            <input type="hidden" name="Member_id" value="<?php echo ($_SESSION["Member_id"]); ?>"/>
                            <p>Are you sure you want to delete the record for <?php echo ($_SESSION["Member_id"]); ?>?</p><br>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="index.php" class="btn btn-default">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>