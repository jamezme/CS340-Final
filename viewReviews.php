<?php
	session_start();
    // Include config file
    require_once "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Projects</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper{
            width: 650px;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
	   <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">View Reviews</h2>
						<a href="addReview.php" class="btn btn-success pull-right">Add Review (TODO)</a>
                    </div>
<?php

// Check existence of id parameter before processing further
if(isset($_GET["Book_id"]) && !empty(trim($_GET["Book_id"]))){
	$_SESSION["Book_id"] = $_GET["Book_id"];
}
if(isset($_GET["title"]) && !empty(trim($_GET["title"]))){
	$_SESSION["title"] = $_GET["title"];
}

if(isset($_SESSION["Book_id"]) ){
	
    // Prepare a select statement
    $sql = "SELECT MEMBER.fname, REVIEW.Rating, REVIEW.Subject, REVIEW.Description
            FROM REVIEW
            LEFT JOIN MEMBER ON REVIEW.Reviewer = MEMBER.Member_id
            WHERE REVIEW.Reviewed_book = ?";

	//$sql = "SELECT Essn, Pno, Hours From WORKS_ON WHERE Essn = ? ";   
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_Book_id);      
        // Set parameters
       $param_Book_id = $_SESSION["Book_id"];
	   $Title = $_SESSION["title"];

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
			echo"<h4> Reviews for ".$Title." &nbsp      Book_id = ".$param_Book_id."</h4><p>";
			if(mysqli_num_rows($result) > 0){
				echo "<table class='table table-bordered table-striped'>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th>Reviewer</th>";
                            echo "<th>Rating</th>";
                            echo "<th>Subject</th>";
                            echo "<th>Description</th>";
                        echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";							
				// output data of each row
                    while($row = mysqli_fetch_array($result)){
                        echo "<tr>";
                        echo "<td>" . $row['fname'] . "</td>";
                        echo "<td>" . $row['Rating'] . "</td>";
                        echo "<td>" . $row['Subject'] . "</td>";
                        echo "<td>" . $row['Description'] . "</td>";
    
                        echo "</tr>";
                    }
                    echo "</tbody>";                            
                echo "</table>";				
				mysqli_free_result($result);
			} else {
				echo "No Reviews. ";
			}
//				mysqli_free_result($result);
        } else{
			// URL doesn't contain valid id parameter. Redirect to error page
            header("location: error.php");
            exit();
        }
    }     
    // Close statement
    mysqli_stmt_close($stmt);
    
    // Close connection
    mysqli_close($link);
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>					                 					
	<p><a href="index.php" class="btn btn-primary">Back</a></p>
    </div>
   </div>        
  </div>
</div>
</body>
</html>