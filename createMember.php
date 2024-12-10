<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$Member_id = $Fname = $Lname = "";
$Member_id_err = $Lname_err = $Fname_err = "" ;
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate First name
    $Fname = trim($_POST["Fname"]);
    if(empty($Fname)){
        $Fname_err = "Please enter a Fname.";
    } elseif(!filter_var($Fname, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $Fname_err = "Please enter a valid Fname.";
    } 
    // Validate Last name
    $Lname = trim($_POST["Lname"]);
    if(empty($Lname)){
        $Lname_err = "Please enter a Lname.";
    } elseif(!filter_var($Lname, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $Lname_err = "Please enter a valid Lname.";
    } 
 
    // Check input errors before inserting in database
    if(empty($Lname_err) && empty($Fname_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO MEMBER (fname, lname) 
		        VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_Fname, $param_Lname);
            
            // Set parameters
            $param_Lname = $Lname;
			$param_Fname = $Fname;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
				    header("location: index.php");
					exit();
            } else{
                echo "<center><h4>Error while creating new member</h4></center>";
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
    <title>Create Record</title>
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
                    <p>Please fill this form and submit to add a Member record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                 
						<div class="form-group <?php echo (!empty($Fname_err)) ? 'has-error' : ''; ?>">
                            <label>First Name</label>
                            <input type="text" name="Fname" class="form-control" value="<?php echo $Fname; ?>">
                            <span class="help-block"><?php echo $Fname_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($Lname_err)) ? 'has-error' : ''; ?>">
                            <label>Last Name</label>
                            <input type="text" name="Lname" class="form-control" value="<?php echo $Lname; ?>">
                            <span class="help-block"><?php echo $Lname_err;?></span>
                        </div>
						
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="viewMembers.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
