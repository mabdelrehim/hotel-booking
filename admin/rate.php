<?php
session_start();
if(isset($_SESSION['Username'])) {
    if($_SESSION['Type'] == 'broker'){
        header('Location: broker.php');
        exit();
    }
    elseif ($_SESSION['Type'] == 'hotel') {
        header('Location: hotel.php');
        exit();
    }
    elseif ($_SESSION['Type']  =='customer') {
        if ($_SESSION['suspended'] == 1) {
            header('Location: sus.php');
            exit();
        }

        include 'connect.php';




        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            $stmtRate = $con->prepare("INSERT INTO ratings(ratedHotelId, ratingCustomerId, numberOfStars)
                                                  VALUES (?, ?, ?)");
            $stmtRate->execute(array($_SESSION['hidForRate'], $_SESSION['id'], $_POST['rating']));

            $stmtCalculateAvg = $con->prepare("SELECT AVG(numberOfStars) AS avg FROM ratings");
            $stmtCalculateAvg->execute();
            $row = $stmtCalculateAvg->fetch();

            $stmtUpdateHotel = $con->prepare("UPDATE hotel SET avgRating = ? WHERE hotelId=?");
            $stmtUpdateHotel->execute(array($row['avg'], $_SESSION['hidForRate']));
            header("Location: customer.php");

        }


        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8"/>
            <title> Rate Page</title>
            <link rel="stylesheet" href="layout/css/bootstrap.min.css">
            <link rel="stylesheet" href="layout/css/font-awesome.min.css">
            <link rel="stylesheet" href="layout/css/style.css">
        </head>
        <body>
        <a href="logout.php">Logout</a>


        <h1 class="text-center">Add Rating</h1>
        <div class="container">
            <form class="form-horizontal" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10 col-md-4">
                        <input type="text" name="name" class="form-control"  required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Rating</label>
                    <div class="col-sm-10 col-md-4">
                        <input type="number" name="rating" class="form-control input-sm" min="0" max="5" required="required">
                    </div>
                </div>



                <div class="form-group">

                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" value="Add Rating" class="btn btn-primary btn-lg">
                    </div>
                </div>

            </form>
        </div>

        <?php





    }
}

	include "includes/templates/footer.html";
?>

