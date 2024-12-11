<?php
    session_start();
    //$currentpage="View Authors"; 
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
    ?>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                       <h2 class="pull-left">Author Details</h2>
                    </div>
                    <?php
                    // Include config file
                    require_once "config.php";
                    
                    // Attempt select all author and book count query execution
                    $sql = "SELECT A.Author_id, A.Author_fname, A.Author_lname, A.Rating, COUNT(BA.book_id) AS num_books_written
                            FROM AUTHOR A
                            LEFT JOIN BOOK_AUTHOR BA ON A.Author_id = BA.author_id
                            GROUP BY A.Author_id, A.Author_fname, A.Author_lname, A.Rating";
                    
                    if ($result = mysqli_query($link, $sql)) {
                        if (mysqli_num_rows($result) > 0) {
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th width=8%>Author ID</th>";
                                        echo "<th width=10%>First Name</th>";
                                        echo "<th width=10%>Last Name</th>";
                                        echo "<th width=10%>Rating</th>";
                                        echo "<th width=8%>Books in Library</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>";
                                        echo "<td>" . $row['Author_id'] . "</td>";
                                        echo "<td>" . $row['Author_fname'] . "</td>";
                                        echo "<td>" . $row['Author_lname'] . "</td>";
                                        echo "<td>" . $row['Rating'] . "</td>";
                                        echo "<td>" . $row['num_books_written'] . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else {
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else {
                        echo "ERROR: Could not able to execute $sql. <br>" . mysqli_error($link);
                    }
                    ?>
                    <p><a href="index.php" class="btn btn-primary">Back</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

