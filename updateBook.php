<?php
session_start();	
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$Title = $Genre = $Length = $Author_fname = $Author_lname ="" ;
$Title_err = $Genre_err =  $Length_err = $Author_fname_err = $Author_lname_err = "" ;

// Form default values
if(isset($_GET["Book_id"]) && !empty(trim($_GET["Book_id"]))){
	$_SESSION["Book_id"] = $_GET["Book_id"];

    // Prepare SELECT statement
    $sql1 = "SELECT BOOK.Title, BOOK.Genre, BOOK.Length, AUTHOR.Author_fname, AUTHOR.Author_lname
             FROM BOOK
             JOIN BOOK_AUTHOR ON BOOK.Book_id = BOOK_AUTHOR.Book_id
             JOIN AUTHOR ON BOOK_AUTHOR.Author_id = AUTHOR.Author_id
             WHERE BOOK.Book_id = ?";
  
    if($stmt1 = mysqli_prepare($link, $sql1)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt1, "i", $param_Book_id);      
        // Set parameters
       $param_Book_id = trim($_GET["Book_id"]);

        // Execute the SELECT statement
        if(mysqli_stmt_execute($stmt1)){
            $result1 = mysqli_stmt_get_result($stmt1);
			if(mysqli_num_rows($result1) > 0){

				$row = mysqli_fetch_array($result1);

				$Title = $row['Title'];
                $Genre = $row['Genre'];
                $Length = $row['Length'];
                $Author_fname = $row['Author_fname'];
                $Author_lname = $row['Author_lname'];
			}
		}
	}
}
 

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // the id is hidden and can not be changed
    $Book_id = $_SESSION["Book_id"];

    // Validate title
    $Title = trim($_POST["Title"]);
    if (empty($Title)) {
        $Title_err = "Please enter a title.";
    }

    // Validate genre
    $Genre = trim($_POST["Genre"]);
    if (empty($Genre)) {
        $Genre_err = "Please enter a genre.";
    } elseif (!filter_var($Genre, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z\s]+$/")))) {
        $Genre_err = "Please enter a valid genre.";
    }

    // Validate length
    $Length = trim($_POST["Length"]);
    if (empty($Length)) {
        $Length_err = "Please enter the book length.";
    } elseif (!is_numeric($Length)) {
        $Length_err = "Book length must be a number.";
    }

	// Validate author_fname
    $Author_fname = trim($_POST["Author_fname"]);
    if (empty($Author_fname)) {
        $Author_fname_err = "Please enter the author's first name.";
    }

    // Validate author_lname
    $Author_lname = trim($_POST["Author_lname"]);
    if (empty($Author_lname)) {
        $Author_lname_err = "Please enter the author's last name.";
    }

        // Check input errors before inserting in database
    if (empty($Title_err) && empty($Genre_err) && empty($Length_err) && empty($Author_fname_err) && empty($Author_lname_err)) {
        // Check if the author exists or not
        $sql = "SELECT author_id FROM AUTHOR WHERE LOWER(author_fname) = LOWER(?) AND LOWER(author_lname) = LOWER(?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $Author_fname, $Author_lname);

            // Execute the SELECT statement
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                // If the author exists in the database, get the result
                if (mysqli_num_rows($result) > 0) {
                    // Get the author_id from the result
                    $row = mysqli_fetch_assoc($result);
                    $Author_id = $row['author_id'];
                } else {
                    // If author doesn't already exist, add to database
                    $sql_add_author = "INSERT INTO AUTHOR (Author_fname, Author_lname) VALUES (?, ?)";
                    if ($stmt_add = mysqli_prepare($link, $sql_add_author)) {
                        // Bind variables to prepared statement
                        mysqli_stmt_bind_param($stmt_add, "ss", $Author_fname, $Author_lname);
                        if (mysqli_stmt_execute($stmt_add)) {
                            // Get the new Author_id after adding
                            $Author_id = mysqli_insert_id($link);
                        } else {
                            echo "<center><h4>Error adding author.</h4></center>";
                            exit();
                        }
                    }
                }
            } else {
                exit();
            }

            // Close the SELECT statement
            mysqli_stmt_close($stmt);
        }

        // Update the existing book details 
        $sql_update_book = "UPDATE BOOK SET Title = ?, Genre = ?, Length = ? WHERE Book_id = ?";
        if ($stmt_update_book = mysqli_prepare($link, $sql_update_book)) {
            // Bind variables to prepared statement
            mysqli_stmt_bind_param($stmt_update_book, "ssii", $Title, $Genre, $Length, $Book_id);
            if (mysqli_stmt_execute($stmt_update_book)) {
                // If successful, update the book_author association
                $sql_update_book_author = "UPDATE BOOK_AUTHOR SET Author_id = ? WHERE Book_id = ?";
                if ($stmt_update_book_author = mysqli_prepare($link, $sql_update_book_author)) {
                    // Bind variables to prepared statement
                    mysqli_stmt_bind_param($stmt_update_book_author, "ii", $Author_id, $Book_id);
                    if (mysqli_stmt_execute($stmt_update_book_author)) {
                        // If successful, redirect to index page
                        header("location: index.php");
                        exit();
                    } else {
                        echo "Error: Could not update author association with the book.";
                    }
                }
            } else {
                echo "Error: Could not update book.";
            }
            mysqli_stmt_close($stmt_update_book);
            mysqli_stmt_close($stmt_update_book_author);
        }
    }

    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Book</title>
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
                        <h3>Update <?php echo $Title; ?> with ID =  <?php echo $_GET["Book_id"]; ?> </H3>
                    </div>
                    <p>Please edit the input values and submit to update.
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			
             
						<div class="form-group <?php echo (!empty($Title_err)) ? 'has-error' : ''; ?>">
                            <label>Book Title</label>
                            <input type="text" name="Title" class="form-control" value="<?php echo $Title; ?>">
                            <span class="help-block"><?php echo $Title_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($Genre_err)) ? 'has-error' : ''; ?>">
                            <label>Genre of the Book</label>
                            <input type="text" name="Genre" class="form-control" value="<?php echo $Genre; ?>">
                            <span class="help-block"><?php echo $Genre_err;?></span>
                        </div>
				
						<div class="form-group <?php echo (!empty($Length_err)) ? 'has-error' : ''; ?>">
                            <label>Length of the Book</label>
                            <input type="text" name="Length" class="form-control" value="<?php echo $Length; ?>">
                            <span class="help-block"><?php echo $Length_err;?></span>
                        </div>
						                  
						<div class="form-group <?php echo (!empty($Author_fname_err)) ? 'has-error' : ''; ?>">
                            <label>Author's First Name</label>
                            <input type="text" name="Author_fname" class="form-control" value="<?php echo $Author_fname; ?>">
                            <span class="help-block"><?php echo $Author_fname_err;?></span>
                        </div>

                        <div class="form-group <?php echo (!empty($Author_lname_err)) ? 'has-error' : ''; ?>">
                            <label>Author's Last Name</label>
                            <input type="text" name="Author_lname" class="form-control" value="<?php echo $Author_lname; ?>">
                            <span class="help-block"><?php echo $Author_lname_err;?></span>
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
