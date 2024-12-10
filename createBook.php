<?php
session_start();

// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$Title = $Genre = $Length = $Author_fname = $Author_lname ="" ;
$Title_err = $Genre_err =  $Length_err = $Author_fname_err = $Author_lname_err = "" ;
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate title
    $Title = trim($_POST["Title"]);
    if(empty($Title)){
        $Title_err = "Please enter a title.";
    }
    
    // Validate genre
    $Genre = trim($_POST["Genre"]);
    if(empty($Genre)){
        $Genre_err = "Please enter a genre.";
    } elseif(!filter_var($Genre, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $Genre_err = "Please enter a valid genre.";
    } 

    // Validate length
    $Length = trim($_POST["Length"]);
    if (empty($Length)) {
        $Length_err = "Please enter the book length.";
    } elseif (!is_numeric($Length)) {
        $Length_err = "Book ength must be a number.";
    }
 
	// Validate author_fname
    $Author_fname = trim($_POST["Author_fname"]);
    if(empty($Author_fname)){
        $Author_fname_err = "Please enter the author's first name.";
    }

    // Validate author_lname
    $Author_lname = trim($_POST["Author_lname"]);
    if(empty($Author_lname)){
        $Author_lname_err = "Please enter the author's last name.";
    }

    // Check input errors before inserting in database
    if(empty($Title_err) && empty($Genre_err) && empty($Length_err) && empty($Author_fname_err) && empty($Author_lname_err)){
        // Get the author_id if author already exists in database
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
} else {
    echo "Error preparing SELECT statement: " . mysqli_error($link) . "<br>";  // Debugging output
    exit();
}

        // Add the new book into the BOOK table
        $sql_add_book = "INSERT INTO BOOK (Title, Genre, Length) VALUES (?, ?, ?)";
        if ($stmt_add_book = mysqli_prepare($link, $sql_add_book)) {
            // Bind variables to prepared statement
            mysqli_stmt_bind_param($stmt_add_book, "ssi", $Title, $Genre, $Length);
            if (mysqli_stmt_execute($stmt_add_book)) {
                // Get the book_id of the newly inserted book
                $Book_id = mysqli_insert_id($link);
                echo "Book ID: " . $Book_id . "<br>";

                // Add to the BOOK_AUTHOR table to associate the book with the author
                $sql_add_book_author = "INSERT INTO BOOK_AUTHOR (Book_id, Author_id) VALUES (?, ?)";
                if ($stmt_add_book_author = mysqli_prepare($link, $sql_add_book_author)) {
                    // Bind variables to prepared statement
                    mysqli_stmt_bind_param($stmt_add_book_author, "ii", $Book_id, $Author_id);
                    echo "Author ID: " . $Author_id . "<br>";
                    if (mysqli_stmt_execute($stmt_add_book_author)) {
                        // If successful, redirect to index page
                        header("location: index.php");
                        exit();
                    } else {
                        echo "Error: Could not associate the author with the book.";
                    }
                }
            } else {
                echo "Error: Could not add book.";
            }
        }
        // Close adding book and adding book_author statements
        mysqli_stmt_close($stmt_add_book);
        mysqli_stmt_close($stmt_add_book_author);

    }
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add A Book</title>
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
                        <h2>Add A Book</h2>
                    </div>
                    
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
