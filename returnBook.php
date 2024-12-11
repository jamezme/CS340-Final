<?php
session_start();

// Get the Member_id and Book_id and store them in variables
if(isset($_GET["Member_id"]) && !empty(trim($_GET["Member_id"]))){
    $_SESSION["Member_id"] = $_GET["Member_id"];
}
if(isset($_GET["Book_id"]) && !empty(trim($_GET["Book_id"]))){
    $_SESSION["Book_id"] = $_GET["Book_id"];
}


$Member_id = isset($_SESSION["Member_id"]) ? $_SESSION["Member_id"] : '';
$Book_id = isset($_SESSION["Book_id"]) ? $_SESSION["Book_id"] : '';


// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$Return_date = "";
$Return_err = $Book_id_err = "" ;
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate Return date
    $Return_date = trim($_POST["Return_date"]);
    if(empty($Return_date)){
        $Return_err = "Please enter the date.";
    }
    $Book_id = trim($_POST["Book_id"]);
    if(empty($Book_id)){
        $Book_id_err = "No book to return";
    }
 
    // Check input errors before inserting in database
    if(empty($Return_err) && empty($Book_id_err)){
        // Prepare an update statement
        $sql = "UPDATE CHECK_OUT
		        SET Return_date = ?
                WHERE Member_id = ? AND Book_id = ?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sii", $param_Return, $param_Member_id, $param_Book_id);
            
            // Set parameters
            $param_Return = $Return_date;
			$param_Member_id = $Member_id;
            $param_Book_id = $Book_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
				    header("location: index.php");
					exit();
            } else{
                echo "<center><h4>Invalid date or no book to return</h4></center>";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Return Book</title>
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
                        <h2>Create Record</h2>
                    </div>
                    <p>Please enter return date.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                 
						<div class="form-group <?php echo (!empty($Return_err)) ? 'has-error' : ''; ?>">
                            <label>Return Date</label>
                            <input type="date" name="Return_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            <span class="help-block"><?php echo $Return_err;?></span>
                        </div>
						
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
