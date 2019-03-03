<?php
session_start();
if(isset($_SESSION['Username'])) {
    if ($_SESSION['Type'] == 'broker') {
        header('Location: broker.php');
        exit();
    } elseif ($_SESSION['Type'] == 'hotel') {
        header('Location: hotel.php');
        exit();
    } elseif ($_SESSION['Type'] == 'customer') {
        if ($_SESSION['suspended'] == 1) {
            header('Location: sus.php');
            exit();
        }

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8"/>
            <title> Hotel Page</title>
            <link rel="stylesheet" href="layout/css/bootstrap.min.css">
            <link rel="stylesheet" href="layout/css/font-awesome.min.css">
            <link rel="stylesheet" href="layout/css/style.css">
        </head>
    <body>
    <a href="logout.php">Logout</a>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <input class="form-control" type="text" name="days" placeholder="days" required="required">
        <input class="form-control" type="text" name="hours" placeholder="hours" required="required">
        <input class="btn btn-primary btn-block loginn" type="submit" value="Advance Time">
    </form>
    <a href="customer.php?do=search&customerId=<?php echo $_SESSION["id"]; ?>">
        <button>Search for Accommodation</button>
    </a>
    <a href="customer.php?do=allReservations&customerId=<?php echo $_SESSION["id"]; ?>">
        <button>View All Reservations</button>
    </a>

        <?php

        //suspend customer logic
        // e3melo suspend law kan 3ando reservations approved embare7 w mara7sh ye3mel check in
        $susStmt = $con->prepare("
                                  SELECT
	                                reservations.customerId, reservations.resrvationId
                                  FROM
	                                reservations LEFT JOIN checkIns ON reservations.customerId = checkIns.customerId
                                  WHERE
	                                checkIns.customerId IS NULL AND reservations.fromDate = ? AND 
	                                reservations.customerId = ? AND reservations.isApproved = 1 AND 
	                                reservation.isCancelled = 0");
        $yesterdaysDate = date('Y-m-d H:i:s', $_SESSION['time'] - (60*60*24));
        $susStmt->execute(array($yesterdaysDate, $_SESSION['id']));
        $rows = $susStmt->fetchAll();
        $count = $susStmt->rowCount();

        if ($count > 0) {
            // cancel those reservations and suspend customer
            foreach ($rows as $row) {
                // cancel each reservation he made
                $stmtCancel = $con->prepare("UPDATE reservations SET isCancelled = 1
                                                      WHERE reservationId = ?");
                $stmtCancel->execute(array($row['reservationId']));
            }
            // suspend the customer
            $stmtSuspend = $con->prepare("INSERT INTO 
                                                      suspendedCustomersAccounts(suspendedCusomerId, suspensionDate)
                                                    VALUES
                                                      (?, ?)");
            $todaysDate = date('Y-m-d H:i:s', strtotime($_SESSION['time']));
            $stmtSuspend->execute(array($_SESSION['id'], $todaysDate));
        }


        if (isset($_GET['do'])) {
            if ($_GET['do'] == 'search') {
                ?>
    <a href="logout.php">Logout</a>
    <h1 class="text-center">Search for Accomodation</h1>

    <div class="container">

        <form class="form-horizontal" method="GET" action="foundhotels.php">


            <div class="form-group">
                <label class="col-sm-2 control-label">Check In</label>
                <div class="col-sm-10 col-md-4">
                    <input type="date" id="start" name="startDate" min="2018-01-01" max="2019-12-12" required="required" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">Check Out</label>
                <div class="col-sm-10 col-md-4">
                    <input type="date" id="end" name="endDate" min="2018-01-01" max="2019-12-12" required="required" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">Location</label>
                <div class="col-sm-10 col-md-4">
                    <input type="text" name="location" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">Hotel Average Rating</label>
                <div class="col-sm-10 col-md-4">
                    <input type="number" name="avgRating" class="form-control" min="0" max="5">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">Hotel Stars</label>
                <div class="col-sm-10 col-md-4">
                    <input type="number" name="stars" class="form-control" min="0" max="7">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">Minimum Price</label>
                <div class="col-sm-10 col-md-4">
                    <input type="number" name="minPrice" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">Maximum Price</label>
                <div class="col-sm-10 col-md-4">
                    <input type="number" name="maxPrice" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">Room Type</label>
                <div class="col-sm-10 col-md-4">
                    <label class="col-sm-2 control-label">Single</label>
                    <input type="radio" name="typeRadio" class="form-control" value="single">
                    <label class="col-sm-2 control-label">Double</label>
                    <input type="radio" name="typeRadio" class="form-control" value="double">
                    <label class="col-sm-2 control-label">Triple</label>
                    <input type="radio" name="typeRadio" class="form-control" value="triple">
                    <label class="col-sm-2 control-label">Royal Suite</label>
                    <input type="radio" name="typeRadio" class="form-control" value="royal">
                </div>
            </div>

            <div class="form-group">

                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" value="Search" class="btn btn-primary btn-lg">
                </div>
            </div>


        </form>

    </div>
                <?php
            }

            elseif ($_GET['do'] == 'allReservations') {

                $stmt= $con->prepare("SELECT 
                                                  hotel.name, reservations.hotelId, reservations.reservationId, 
                                                  reservations,roomId, reservations.fromDate, reservations.toDate, 
                                                  reservations.totalPrice 
                                                FROM 
                                                  reservations INNER JOIN hotel ON hotel.hotelId = reservations.hotelId
                                                WHERE 
                                                  reservations.customerId = ?");
                $stmt->execute(array($_SESSION['id']));
                $rows=$stmt->fetchAll();

                ?>

                <h1 class="text-center">Pending Reservarion Requests</h1>
                <div class="container">
                <div class="table-responsive">
                    <table class="main-table text-center table table-bordered">
                        <tr>
                            <td>Hotel Name</td>
                            <td>Room Number</td>
                            <td>From</td>
                            <td>To</td>
                            <td>Total Price</td>
                            <td>Rate/Cancel Reservation</td>


                        </tr>
                        <?php
                        foreach ($rows as $row) {

                            echo "<tr>";
                            echo 	"<td>" . $row['name'] ."</td>";
                            echo 	"<td>" . $row['roomId'] ."</td>";
                            echo 	"<td>" . $row['fromDate'] ."</td>";
                            echo 	"<td>" . $row['toDate'] ."</td>";
                            echo 	"<td>" . $row['totalPrice'] ."</td>";

                            if ($row['fromDate'] >= date("Y-m-d", $_SESSION['time'])) {
                                echo	"<td>
								        <a href='customer.php?do=allReservations&cancel=true&rid=".$row['reservationId'] ."' class='btn btn-danger'>
								            Cancel
								        </a>
							        </td> ";
                            } else {
                                echo	"<td>
								        <a href='rate.php?cid=".$_SESSION['id']."&hid=".$row['hotelId'] ."' class='btn btn-success'>
								            Rate
								        </a>
							        </td> ";
                            }
                            echo "</tr>";
                        }
                        ?>

                    </table>

                </div>
                <?php
                 if (isset($_GET['cancel'])) {
                    if ($_GET['cancel'] == 'true') {
                        //todo cancel logic
                        $stmtCancelReservation = $con->prepare("UPDATE reservations SET isCancelled=1
                                                                            WHERE reservationId = ?");
                        $stmtCancelReservation->execute(array($_GET['rid']));

                        $_GET['cancel'] = 'false';
                    }
                }

            }
        }



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $days = $_POST['days'];
            $hours = $_POST['hours'];
            $_SESSION['time'] = strtotime("today + " . $days . "days" . " + " . $hours . " hours");
        }
    }
}
?>