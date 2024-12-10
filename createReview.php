<?php
session_start();

// Get the book_id and title and store them in variables
if(isset($_GET["Book_id"]) && !empty(trim($_GET["Book_id"]))){
    $_SESSION["Book_id"] = $_GET["Book_id"];
}

if(isset($_GET["title"]) && !empty(trim($_GET["title"]))){
    $_SESSION["title"] = $_GET["title"];
}

$Reviewed_book = isset($_SESSION["Book_id"]) ? $_SESSION["Book_id"] : '';
$Reviewed_book_title = isset($_SESSION["title"]) ? $_SESSION["title"] : '';


// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$Reviewer = $Rating = $Subject = $Description ="" ;
$Reviewer_err = $Rating_err =  $Subject_err =$Description_err= "" ;
 

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate reviewer id
    $Reviewer = trim($_POST["Reviewer"]);
    if(empty($Reviewer)){
        $Reviewer_err = "Please enter a member ID number.";
    } else {
        // Check if the member_id exists in the database
        $sql = "SELECT Member_id FROM MEMBER WHERE Member_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to prepared statement
            mysqli_stmt_bind_param($stmt, "i", $Reviewer);
            // Execute SELECT statement
            if (mysqli_stmt_execute($stmt)) {
                // Get the result and check if member was found
                $result = mysqli_stmt_get_result($stmt); 
                // Store reviewer if member was found
                if ($row = mysqli_fetch_assoc($result)) {
                    $Reviewer = $row['Member_id'];
                // Member wasn't found
                } else {
                    $Reviewer_err = "No member found with this ID.";
                }
                
            }
            // Close SELECT statement
            mysqli_stmt_close($stmt);
        }
    } 

    // Validate rating
    $Rating = trim($_POST["Rating"]);
    if(empty($Rating)){
        $Rating_err = "Please enter a rating (1-5).";
    } elseif ($Rating < 1 || $Rating > 5) {
        $Rating_err = "Rating must be between 1 and 5.";
    }
 
	// Validate subject
    $Subject = trim($_POST["Subject"]);
    if (empty($Subject)) {
        $Subject_err = "Please enter a Subject.";
    } elseif (strlen($Subject) > 512) {
        $Subject_err = "Subject cannot be more than 512 characters.";
    }

    // Validate description 
    $Description = trim($_POST["Description"]);
    if (empty($Description)) {
        $Description_err = "Please enter a description.";
    } elseif (strlen($Description) > 1024) {
        $Description_err = "Description cannot be more than 1024 characters.";
    }


    // Check input errors before inserting in database
    if(empty($Reviewer_err) && empty($Reviewed_book_err) && empty($Rating_err) && empty($Subject_err) && empty($Description_err)){
        // Prepare insert statement
        $sql = "INSERT INTO REVIEW (Reviewer, Reviewed_book, Rating, Subject, Description) 
		        VALUES (?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iiiss", $param_Reviewer, $param_Reviewed_book, $param_Rating, 
                                    $param_Subject, $param_Description);
           
            // Set parameters
			$param_Reviewer = $Reviewer;
			$param_Reviewed_book = $Reviewed_book;
			$param_Rating = $Rating;
			$param_Subject = $Subject;
            $param_Description = $Description;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // If successful, redirect to index page
				    header("location: index.php");
					exit();
            } else{
                echo "<center><h4>Error while creating new review.</h4></center>";
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
    <title>Add Review</title>
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
                        <h2>Add Review</h2>
						<?php echo "<h3> For ".$Reviewed_book_title." </h3>"?> 
                    </div>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
             
						<div class="form-group <?php echo (!empty($Reviewer_err)) ? 'has-error' : ''; ?>">
                            <label>Member ID</label>
                            <input type="text" name="Reviewer" class="form-control" value="<?php echo $Reviewer; ?>">
                            <span class="help-block"><?php echo $Reviewer_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($Relationship_err)) ? 'has-error' : ''; ?>">
                            <label>Rating (1-5)</label>
                            <input type="number" name="Rating" class="form-control" value="<?php echo $Rating; ?>">
                            <span class="help-block"><?php echo $Rating_err;?></span>
                        </div>
				
						<div class="form-group <?php echo (!empty($Subject_err)) ? 'has-error' : ''; ?>">
                            <label>Subject</label>
                            <input type="text" name="Subject" class="form-control" value="<?php echo $Subject; ?>">
                            <span class="help-block"><?php echo $Subject_err;?></span>
                        </div>
						                  
                        <div class="form-group <?php echo (!empty($Description_err)) ? 'has-error' : ''; ?>">
                            <label>Description</label>
                            <textarea name="Description" class="form-control" rows="5" cols="50"><?php echo $Description; ?></textarea>
                            <span class="help-block"><?php echo $Description_err;?></span>
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
