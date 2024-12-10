<?php
	session_start();	
// Include config file
	require_once "config.php";
 
// Define variables and initialize with empty values
// Note: You can not update Member_id 
$Lname = $Fname = "";
$Lname_err = $Fname_err = "" ;
// Form default values

if(isset($_GET["Member_id"]) && !empty(trim($_GET["Member_id"]))){
	$_SESSION["Member_id"] = $_GET["Member_id"];

    // Prepare a select statement
    $sql1 = "SELECT * FROM MEMBER WHERE Member_id = ?";
  
    if($stmt1 = mysqli_prepare($link, $sql1)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt1, "s", $param_Member_id);      
        // Set parameters
       $param_Member_id = trim($_GET["Member_id"]);

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt1)){
            $result1 = mysqli_stmt_get_result($stmt1);
			if(mysqli_num_rows($result1) > 0){

				$row = mysqli_fetch_array($result1);

				$Lname = $row['lname'];
				$Fname = $row['fname'];
			}
		}
	}
}
 
// Post information about the member when the form is submitted
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // the id is hidden and can not be changed
    $Member_id = $_SESSION["Member_id"];
    // Validate form data this is similar to the create Member file
    // Validate name
    $Fname = trim($_POST["Fname"]);

    if(empty($Fname)){
        $Fname_err = "Please enter a first name.";
    } elseif(!filter_var($Fname, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $Fname_err = "Please enter a valid first name.";
    } 
    $Lname = trim($_POST["Lname"]);
    if(empty($Lname)){
        $Lname_err = "Please enter a last name.";
    } elseif(!filter_var($Lname, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $Lname_err = "Please enter a valid last name.";
    }  
    

    // Check input errors before inserting into database
    if(empty($Fname_err) && empty($Lname_err)){
        // Prepare an update statement
        $sql = "UPDATE MEMBER SET fname=?, lname=? WHERE Member_id=?";
    
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssi", $param_Fname, $param_Lname, $param_Member_id);
            
            // Set parameters
            $param_Fname = $Fname;
			$param_Lname = $Lname;            
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "<center><h2>Error when updating</center></h2>";
            }
        }        
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else {

    // Check existence of sID parameter before processing further
	// Form default values

	if(isset($_GET["Member_id"]) && !empty(trim($_GET["Member_id"]))){
		$_SESSION["Member_id"] = $_GET["Member_id"];

		// Prepare a select statement
		$sql1 = "SELECT * FROM MEMBER WHERE Member_id = ?";
  
		if($stmt1 = mysqli_prepare($link, $sql1)){
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt1, "s", $param_Member_id);      
			// Set parameters
			$param_Member_id = trim($_GET["Member_id"]);

			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt1)){
				$result1 = mysqli_stmt_get_result($stmt1);
				if(mysqli_num_rows($result1) == 1){

					$row = mysqli_fetch_array($result1);

					$Lname = $row['lname'];
					$Fname = $row['fname'];
					
				} else{
					// URL doesn't contain valid id. Redirect to error page
					header("location: error.php");
					exit();
				}                
			} else{
				echo "Error in Member_id while updating";
			}		
		}
			// Close statement
			mysqli_stmt_close($stmt1);
        
			// Close connection
			mysqli_close($link);
	}  else{
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
    <title>Library DB</title>
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
                        <h3>Update Record for Member_id =  <?php echo $_GET["Member_id"]; ?> </H3>
                    </div>
                    <p>Please edit the input values and submit to update.
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
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
                        <input type="hidden" name="Ssn" value="<?php echo $Ssn; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="viewMembers.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>