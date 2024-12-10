<?php
session_start();

// Get the book_id and title and store them in variables
if(isset($_GET["Member_id"]) && !empty(trim($_GET["Member_id"]))){
    $_SESSION["Member_id"] = $_GET["Member_id"];
}



$Member_id = isset($_SESSION["Member_id"]) ? $_SESSION["Member_id"] : '';


// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$Book_id = $Checkout = "";
$Member_id_err = $Book_id_err = $Checkout_err = "" ;
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate First name
    $Book_id = trim($_POST["Book_id"]);
    if(empty($Book_id)){
        $Book_id_err = "Please select a book.";
    }
    // Validate Last name
    $Checkout = trim($_POST["Checkout"]);
    if(empty($Checkout)){
        $Checkout_err = "Please enter the date.";
    }
 
    // Check input errors before inserting in database
    if(empty($Book_id_err) && empty($Checkout_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO CHECK_OUT (Member_id, Book_id, Checkout_date, Return_date) 
		        VALUES (?, ?, ?, NULL)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iis", $param_Member_id, $param_Book_id, $param_Checkout);
            
            // Set parameters
            $param_Member_id = $Member_id;
			$param_Book_id = $Book_id;
            $param_Checkout = $Checkout;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
				    header("location: index.php");
					exit();
            } else{
                echo "<center><h4>Book not available or Member already checked out book.</h4></center>";
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
    <title>Create Checkout Record</title>
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
                    <p>Please fill this form and submit to checkout a book.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                 
						<div class="form-group <?php echo (!empty($Book_id_err)) ? 'has-error' : ''; ?>">
                            <label>Book</label>
                            <input type="text" name="Book_id" class="form-control" value="<?php echo $Book_id; ?>">
                            <span class="help-block"><?php echo $Book_id_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($Checkout_err)) ? 'has-error' : ''; ?>">
                            <label>Date</label>
                            <input type="date" name="Checkout" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            <span class="help-block"><?php echo $Checkout_err;?></span>
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
