
<?php
session_start();
if(isset($_SESSION['Username'])){ //check if hotel is logged in
	    if($_SESSION['Type'] == 'broker'){
	 		header('Location: broker.php');
     		exit();
		}
		elseif ($_SESSION['Type'] == 'hotel') {
        	header('Location: hotel.php');
     		exit();
    	}
    	elseif ($_SESSION['Type']  =='customer') {
    		if($_SESSION['suspended'] == 0){

       			header('Location: customer.php');
   			  	exit();
   			} else {
   				header('Location: sus.php');
   			  	exit();

   			}

    	}

	} else {           // not logged in

        include 'connect.php';
        $tpl='includes/templates/';
        include 'includes/functions/function.php';
        include 'includes/languages/english.php';
        ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title> New Member Page</title>
    <link rel="stylesheet" href="layout/css/bootstrap.min.css">
    <link rel="stylesheet" href="layout/css/font-awesome.min.css">
    <link rel="stylesheet" href="layout/css/style.css">
</head>
<body>
        <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST'){


            // queries logic
            $errorsBool = false;

            // see if the email or username already exist
            $stmtUsername=$con->prepare("SELECT * FROM customer WHERE username= ? ");
            $stmtUsername->execute(array($_POST['username']));
            $countUsername=$stmtUsername->rowCount();
            $rowUsername=$stmtUsername->fetch();

            $stmtEmail=$con->prepare("SELECT * FROM customer WHERE email= ? ");
            $stmtEmail->execute(array($_POST['email']));
            $countEmail=$stmtEmail->rowCount();
            $rowEmail=$stmtEmail->fetch();

            if ($countEmail >= 1 && $countUsername >= 1) {

                $msg = "username and email already exist";
                echo "<div class=\"error\">" .$msg. "</div>";
                $errorBool = true;

            }
            elseif ($countUsername >= 1) {

                $msg = "username already exists";
                echo "<div class=\"error\">" .$msg. "</div>";
                $errorBool = true;

            }
            elseif ($countEmail >= 1) {

                $msg = "email already exists";
                echo "<div class=\"error\">" .$msg. "</div>";
                $errorBool = true;

            } else {

                // the username and email are unique
                // add a new hotel and add it to the pending hotels
                $stmtInsertHotel = $con->prepare("INSERT INTO 
                                                                customer(username, email, password, name) 
                                                              VALUES
														        (?, ?, ?, ?)");
                $stmtInsertHotel->execute(array($_POST['username'], $_POST['email'], $_POST['password'], $_POST['Name']));
                header("Location: login.php");
            }
        } ?>




<h1 class="text-center">Register</h1>
<div class="container">
    <form class="form-horizontal" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
        <div class="form-group">
            <label class="col-sm-2 control-label">Username</label>
            <div class="col-sm-10 col-md-4">
                <input type="text" name="username" class="form-control"  required="required">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Password</label>
            <div class="col-sm-10 col-md-4">
                <input type="Password" name="password" class="password form-control" required="required">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Email</label>
            <div class="col-sm-10 col-md-4">
                <input type="text" name="email" class="form-control" required="required">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Name</label>
            <div class="col-sm-10 col-md-4">
                <input type="text" name="Name" class="form-control" required="required">
            </div>
        </div>


        <div class="form-group">

            <div class="col-sm-offset-2 col-sm-10">
                <input type="submit" value="Register" class="btn btn-primary btn-lg">
            </div>
        </div>

    </form>
</div>
<?php
    include "includes/templates/footer.html";

} ?>
