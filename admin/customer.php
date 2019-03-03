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
        include 'connect.php';

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8"/>
            <title> Member Page</title>
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
        <a href="customer.php?do=search&customerId=<?php echo $_SESSION['id']; ?>">
            <button>Search for Accommodation</button>
        </a>
        <a href="customer.php?do=allReservations&customerId=<?php echo $_SESSION['id']; ?>">
            <button>View All Reservations</button>
        </a>

        <?php

        //suspend customer logic
        // e3melo suspend law kan 3ando reservations approved embare7 w mara7sh ye3mel check in
        $susStmt = $con->prepare("
                                  SELECT
	                                reservations.customerId, reservations.reservationId
                                  FROM
	                                reservations LEFT JOIN checkIns ON reservations.reservationId = checkIns.reservationId 
                                  WHERE checkIns.reservationId IS NULL AND reservations.fromDate < ? AND 
                                        reservations.customerId = ? AND reservations.isCancelled = 0 AND 
                                        reservations.isApproved = 1");
        $yesterdaysDate = date('Y-m-d', $_SESSION['time']);
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

                $stmtGetNum = $con->prepare("SELECT numberOfReservationsMade FROM customer WHERE customerId = ?");
                $stmtGetNum->execute(array($_SESSION['id']));
                $row = $stmtGetNum->fetch();
                $newNum = $row['numberOfReservationsMade'] - 1;

                $stmtUpdateNum = $con->prepare("UPDATE customer SET numberOfReservationsMade = ? WHERE customerId = ?");
                $stmtUpdateNum->execute(array($newNum, $_SESSION['id']));
            }
            // suspend the customer
            $stmtSuspend = $con->prepare("INSERT INTO 
                                                      suspendedCustomersAccounts(suspendedCustomerId, suspensionDate)
                                                    VALUES
                                                      (?, ?)");
            $todaysDate = date('Y-m-d H:i:s', strtotime($_SESSION['time']));
            $stmtSuspend->execute(array($_SESSION['id'], $todaysDate));
            // redirect him to sus.php
            header("Location: sus.php");
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
                // view all not pending reservations

                $stmt= $con->prepare("SELECT 
                                                  hotel.name, reservations.hotelId, reservations.reservationId, 
                                                  reservations.roomId, reservations.fromDate, reservations.toDate, 
                                                  reservations.totalPrice 
                                                FROM 
                                                  reservations INNER JOIN hotel ON hotel.hotelId = reservations.hotelId
                                                  LEFT JOIN pendingReservation ON reservations.reservationId = pendingReservation.pendingReservationId
                                                WHERE 
                                                  pendingReservation.pendingReservationId IS NULL AND
                                                  reservations.customerId = ? AND extensionDummy = 0");
                $stmt->execute(array($_SESSION['id']));
                $rows=$stmt->fetchAll();

                ?>

                <h1 class="text-center">All Reservations</h1>
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
                                $_SESSION['hidForRate'] = $row['hotelId'];
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

                        $stmtGetNum = $con->prepare("SELECT numberOfReservationsMade FROM customer WHERE customerId = ?");
                        $stmtGetNum->execute(array($_SESSION['id']));
                        $row = $stmtGetNum->fetch();
                        $newNum = $row['numberOfReservationsMade'] - 1;

                        $stmtUpdateNum = $con->prepare("UPDATE customer SET numberOfReervationsMade = ? WHERE customerId = ?");
                        $stmtUpdateNum->execute(array($newNum, $_SESSION['id']));

                        $_GET['cancel'] = 'false';
                    }
                }

            }
            elseif ($_GET['do'] == 'registrationSent') {

                $stmtMakeReservation = $con->prepare("INSERT INTO
                                                                  reservations(customerId, roomId, hotelId, fromDate, toDate, totalPrice)
                                                                VALUES 
                                                                   (?, ?, ?, ?, ?, ?)");

                $calculatedPrice =
                    ((strtotime($_GET['reservationEnd']) - strtotime($_GET['reservationStart']))/(60*60*24))*($_GET['price']);

                $stmtSeeIfCustomerClassA = $con->prepare("SELECT numberOfReservationsMade FROM customer
                                                                      WHERE customerId = ?");
                $stmtSeeIfCustomerClassA->execute(array($_SESSION['id']));

                $rowClassA = $stmtSeeIfCustomerClassA->fetch();
                if($rowClassA['numberOfReservationsMade'] > 5) {
                    $calculatedPrice = $calculatedPrice*0.95;
                }

                $stmtMakeReservation->execute(array($_SESSION['id'],
                    $_GET['roomId'], $_GET['offeringHotelId'], $_GET['reservationStart'], $_GET['reservationEnd'], $calculatedPrice));

                $id = $con->lastInsertId();
                $stmtMakeItPending = $con->prepare("INSERT INTO pendingReservation(pendingReservationId)
                                                              VALUES(?)");
                $stmtMakeItPending->execute(array($id));

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