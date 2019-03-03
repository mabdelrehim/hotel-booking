<?php
session_start();
if (isset($_SESSION['Username']) && isset($_SESSION['Type'])) {
    if($_SESSION['Type'] == 'broker'){
        header('Location: broker.php');
        exit();
    }

    elseif ($_SESSION['Type']== 'hotel') {
        header('Location: hotel.php');
        exit();
    }
    elseif ($_SESSION['Type']=='customer') {
        if ($_SESSION['suspended'] == 1) {
            header('Location: sus.php');
            exit();
        }
        else{
            header('Location: customer.php');
            exit();

        }
    }

}
include 'connect.php';

?>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username=$_POST["username"];
    $password=$_POST["password"];
    $days = $_POST['days'];
    $hours = $_POST['hours'];
    $_SESSION['time'] = strtotime("today + ". $days ."days" . " + " . $hours ." hours");
    $type=$_POST["radio"];

    if($type=="User"){

        // Checking if user exists
        $stmt=$con->prepare("SELECT customerId FROM customer WHERE username =? AND password=? ");   // change users to name of the table
        $stmt->execute(array($username,$password)); // don't forget to make it hashedpass
        $count=$stmt->rowCount();
        $row=$stmt->fetch();


        if($count > 0){ // user exists
            $_SESSION['id']=$row['customerId'];


            $stmt=$con->prepare("SELECT * FROM suspendedCustomersAccounts WHERE suspendedCustomerId= ? ");   // check if user is suspended
            $stmt->execute(array($row['customerId']));
            $count=$stmt->rowCount();
            $row=$stmt->fetch();
            if($count>0 ){   // user suspended

                if( (strtotime("today + ". $days ."days" . " + " . $hours ." hours") - strtotime(row['suspensionDate'] ))
                    / (60*60*24) > (7) ) { //passed suspension date
                    // strtotime("now + 1 day +1 hour ")
                    $stmt=$con->prepare("DELETE  FROM suspendedCustomersAccounts WHERE suspendedCustomerId= ? ");
                    $stmt->execute(array($row['suspendedCustomerId']));
                    $_SESSION['suspended'] = 0;
                    header('Location: customer.php');
                    exit();
                } else {
                    $_SESSION['suspended'] = 1;
                    header('Location: sus.php');
                    exit();
                }
            } else { // not suspended

                $_SESSION['Username']=$username;
                // customer
                $_SESSION['Type']= 'customer';
                $_SESSION['suspended'] = 0;
                header('Location: customer.php');
                exit();
            }


        } // end user found
    } // end user

    if($type=="Hotel"){

        // Checking if hotel exists
        $stmt=$con->prepare("SELECT hotelId FROM hotel WHERE username =? AND password=? ");
        $stmt->execute(array($username,$password));
        $count=$stmt->rowCount();
        $row=$stmt->fetch();
        if($count > 0){

            $_SESSION['Username']=$username;
            $_SESSION['password']=$password;
            // hotel
            $_SESSION['Type']= 'hotel';
            $_SESSION['id']=$row['hotelId'];
            header('Location: hotel.php');
            exit();
        }
    }

    if($type=="Broker"){

        $stmt=$con->prepare("SELECT brokerAccountId FROM brokerAccount WHERE username =? AND password=? ");
        $stmt->execute(array($username,$password));
        $count=$stmt->rowCount();
        $row=$stmt->fetch();
        if($count > 0){

            $_SESSION['Username']=$username;
            // hotel
            $_SESSION['Type']= 'broker';
            header('Location: broker.php');
            exit();
        }
    }

}
?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--===============================================================================================-->
        <link rel="icon" type="image/png" href="login/images/icons/favicon.ico"/>
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="login/vendor/bootstrap/css/bootstrap.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="login/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="login/fonts/iconic/css/material-design-iconic-font.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="login/vendor/animate/animate.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="login/vendor/css-hamburgers/hamburgers.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="login/vendor/animsition/css/animsition.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="login/vendor/select2/select2.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="login/vendor/daterangepicker/daterangepicker.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="login/css/util.css">
        <link rel="stylesheet" type="text/css" href="login/css/main.css">
        <!--===============================================================================================-->
    </head>
<body>

<div class="limiter">
    <div class="container-login100" style="background-image: url('login/images/bg-01.jpg');">
        <div class="wrap-login100">
            <form class="login100-form validate-form" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
					<span class="login100-form-logo">
						<i class="zmdi zmdi-landscape"></i>
					</span>

                <span class="login100-form-title p-b-34 p-t-27">
						Booking.com The Better Version
					</span>


                <div class="wrap-input100 validate-input" data-validate = "Advance Days">
                    <input class="input100" type="number" name="days" placeholder="Advance Days" required="required" >
                    <span class="focus-input100" data-placeholder="&#xf207;"></span>
                </div>

                <div class="wrap-input100 validate-input" data-validate = "Advance Hours">
                    <input class="input100" type="number" name="hours" placeholder="Advance Hours" required="required" >
                    <span class="focus-input100" data-placeholder="&#xf207;"></span>
                </div>

                <div class="wrap-input100 validate-input" data-validate = "Enter username">
                    <input class="input100" type="text" name="username" placeholder="Username">
                    <span class="focus-input100" data-placeholder="&#xf207;"></span>
                </div>

                <div class="wrap-input100 validate-input" data-validate="Enter password">
                    <input class="input100" type="password" name="password" placeholder="Password">
                    <span class="focus-input100" data-placeholder="&#xf191;"></span>
                </div>

                <div class="form-inline">
                    <input class="form-check-input" type="radio" name="radio" id="inlineRadio1" value="Broker">
                    <label class="form-check-label" >Broker</label>
                    <input class="form-check-input" type="radio" name="radio" id="inlineRadio2" value="User">
                    <label class="form-check-label" >User</label>
                    <input class="form-check-input" type="radio" name="radio" id="inlineRadio3" value="Hotel">
                    <label class="form-check-label">Hotel</label>
                </div>

                <div class="container-login100-form-btn">
                    <button class="login100-form-btn" type="submit" name="login_user" value="Login">
                        Login
                    </button>
                </div>

                <div class="text-center p-t-90">
                    <a class="txt1" href="userRegistration.php">
                        Register New Member
                    </a>
                    <br/>
                    <a class="txt1" href="hotelregisteration.php">
                        Register New Hotel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="dropDownSelect1"></div>

<!--===============================================================================================-->
<script src="login/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
<script src="login/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
<script src="login/vendor/bootstrap/js/popper.js"></script>
<script src="login/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
<script src="login/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
<script src="login/vendor/daterangepicker/moment.min.js"></script>
<script src="login/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
<script src="login/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
<script src="login/js/main.js"></script>



<!--  <div id="formm" >

        <form class="login" action="<?php /*echo $_SERVER['PHP_SELF'] */?>"  method="POST">
            <input class="form-control" type="text" name="days" placeholder="days" required="required" >
            <input class="form-control" type="text" name="hours" placeholder="hours" required="required" >
            <h4 class="text-center"> Login </h4>
            <input class="form-control" type="text" name="user" placeholder="Username" autocomplete="off" >
            <input class="form-control" type="password" name="pass" placeholder="Password" autocomplete="new-password" >
            <div class="bla">
                <input class="form-check-input" type="radio" name="radio" id="inlineRadio1" value="Broker">
                <label class="form-check-label" >Broker</label>
                <input class="form-check-input" type="radio" name="radio" id="inlineRadio2" value="User">
                <label class="form-check-label" >User</label>
                <input class="form-check-input" type="radio" name="radio" id="inlineRadio3" value="Hotel">
                <label class="form-check-label">Hotel</label>
            </div>
            <input class="btn btn-primary btn-block loginn" type="submit" value="Login">
        </form>
        <a><button class="btn-danger btn  sign">Register New User</button></a>
        <a href=" hotelregisteration.php "><button class="btn-danger btn  sign1">Register New Hotel</button></a>

    </div>
-->



<?php
include  "includes/templates/footer.html";

?>