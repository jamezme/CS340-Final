<?php
	session_start();
	//$currentpage="View Members"; 
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
		       <h2 class="pull-left">Member Details</h2>
                        <a href="createMember.php" class="btn btn-success pull-right">Add New Member</a>
                    </div>
                    <?php
                    // Include config file
                    require_once "config.php";
                    
                    // Attempt select all member query execution
					
                    $sql = "SELECT MEMBER.Member_id, MEMBER.fname, MEMBER.lname, checked_out.Title, checked_out.Book_id
                            FROM MEMBER
                            LEFT JOIN (SELECT CHECK_OUT.Member_id, BOOK.Title, BOOK.Book_id FROM BOOK NATURAL JOIN CHECK_OUT WHERE Return_date IS NULL)
                            AS checked_out
                            ON checked_out.Member_id = MEMBER.Member_id";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th width=8%>Member_id</th>";
                                        echo "<th width=10%>First Name</th>";
                                        echo "<th width=10%>Last Name</th>";
                                        echo "<th width=10%>Checked Out</th>";
                                        echo "<th width=10%>Actions</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['Member_id'] . "</td>";
                                        echo "<td>" . $row['fname'] . "</td>";
                                        echo "<td>" . $row['lname'] . "</td>";
                                        echo "<td>" . $row['Title'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='updateMember.php?Member_id=". $row['Member_id'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='deleteMember.php?Member_id=". $row['Member_id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                            echo "<a href='checkoutBook.php?Member_id=". $row['Member_id'] ."' title='Checkout Book' data-toggle='tooltip'><span class='glyphicon glyphicon-book'></span></a>";
                                            echo "<a href='returnBook.php?Member_id=". $row['Member_id'] ."&Book_id=". $row['Book_id'] ."' title='Return Book' data-toggle='tooltip'><span class='glyphicon glyphicon-log-in'></span></a>";
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
                    <p><a href="index.php" class="btn btn-primary">Back</a></p>
                </div>

</body>
</html>
