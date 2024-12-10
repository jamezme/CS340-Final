<?php
	session_start();
	//$currentpage="View Books"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library DB</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
	<style type="text/css">
        .wrapper{
            width: 70%;
            margin:0 auto;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
		 $('.selectpicker').selectpicker();
    </script>
</head>
<body>
    <?php
        // Include config file
        require_once "config.php";
//		include "header.php";
	?>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
		    <div class="page-header clearfix">
		       <h2 class="pull-left">Library Book Details</h2>
                        <a href="viewMembers.php" class="btn btn-success pull-right">View Members</a>
                        <a href="viewAuthors.php" class="btn btn-success pull-right">View Authors</a>
                        <a href='createBook.php' class="btn btn-success pull-right">Add a Book</a>
                    </div>
                    <?php
                    // Include config file
                    require_once "config.php";
                    
                    // Attempt select all book query execution
                    $sql = "SELECT BOOK.Book_id, BOOK.title, AUTHOR.Author_fname, AUTHOR.Author_lname, BOOK.Genre, BOOK.Length, BOOK.Rating, IF(CHECK_OUT.Return_date IS NULL, 'YES', 'NO') AS Available
                            FROM BOOK
                            LEFT JOIN CHECK_OUT ON BOOK.Book_id = CHECK_OUT.Book_id
                            LEFT JOIN BOOK_AUTHOR ON BOOK.Book_id = BOOK_AUTHOR.Book_id
                            LEFT JOIN AUTHOR ON BOOK_AUTHOR.Author_id = AUTHOR.Author_id";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th width=8%>Book ID</th>";
                                        echo "<th width=10%>Title</th>";
                                        echo "<th width=10%>Author First Name</th>";
                                        echo "<th width=10%>Author Last Name</th>";
                                        echo "<th width=10%>Genre</th>";
                                        echo "<th width=10%>Length</th>";
                                        echo "<th width=10%>Rating</th>";
                                        echo "<th width=10%>Available</th>";
                                        echo "<th width=10%>Actions</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['Book_id'] . "</td>";
                                        echo "<td>" . $row['title'] . "</td>";
                                        echo "<td>" . $row['Author_fname'] . "</td>";
                                        echo "<td>" . $row['Author_lname'] . "</td>";
                                        echo "<td>" . $row['Genre'] . "</td>";
                                        echo "<td>" . $row['Length'] . "</td>";
                                        echo "<td>" . $row['Rating'] . "</td>";
                                        echo "<td>" . $row['Available'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='viewReviews.php?Book_id=". $row['Book_id']."&title=".$row['title']."' title='View Reviews' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                            echo "<a href='updateBook.php?Book_id=". $row['Book_id'] ."' title='Update Record (TODO)' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='deleteBook.php?Book_id=". $row['Book_id']."&title=".$row['title']."' title='Delete Book' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. <br>" . mysqli_error($link);
                    }
                    ?>
                </div>

</body>
</html>
