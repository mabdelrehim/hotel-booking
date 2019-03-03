<?php
session_start();
if(isset($_SESSION['Username'])) { //check if hotel is logged in
    if ($_SESSION['Type'] == 'broker') { // if loggedd in make sure its the hotel

        include 'connect.php';
        $tpl = 'includes/templates/';
        include 'includes/functions/function.php';
        include 'includes/languages/english.php';

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8"/>
            <title> Broker Page</title>
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


    <a href="broker.php?do=pendingHotels">
        <button>View Pending Hotel Requests</button>
    </a>
    <a href="broker.php?do=allHotels">
        <button>View All Hotels</button>
    </a>
    <a href="broker.php?do=suspendedHotels">
        <button>View Suspended Hotels</button>
    </a>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $days = $_POST['days'];
            $hours = $_POST['hours'];
            $_SESSION['time'] = strtotime("today + " . $days . "days" . " + " . $hours . " hours");
        }
        if(isset($_GET['do'])) {
            if ($_GET['do']=='pendingHotels') {


                $stmtPendingHotels = $con->prepare("SELECT 
                                                      hotel.name, hotel.email, pendingHotelAccounts.pendingHotelId,
                                                      pendingHotelAccounts.premiumRequest
                                                FROM
                                                      hotel INNER JOIN pendingHotelAccounts 
                                                        ON hotel.hotelId = pendingHotelAccounts.pendingHotelId");
                $stmtPendingHotels->execute();
                $rows = $stmtPendingHotels->fetchAll();

                ?>

                <h1 class="text-center">Pending Hotel Requests</h1>
                <div class="container">
                <div class="table-responsive">
                    <table class="main-table text-center table table-bordered">
                        <tr>
                            <td>Hotel Name</td>
                            <td>Hotel Email</td>
                            <td>Requested Premium</td>
                            <td>Approve/Deny</td>

                        </tr>
                        <?php
                        foreach ($rows as $row) {

                            echo "<tr>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            if ($row['premiumRequest'] == 1) {
                                echo "<td>
                                           &#10003;
                                      </td>";
                            } else {
                                echo "<td>
                                           &#10005;
                                      </td>";
                            }
                            echo "<td>
								<a href='broker.php?do=pendingHotels&approve=true&hid=" . $row['pendingHotelId'] . "&premium=".$row['premiumRequest']."' class='btn btn-success'>Approve</a>
								<a href='broker.php?do=pendingHotels&deny=true&hid=" . $row['pendingHotelId'] . "'class='btn btn-danger confirm'>Deny</a>
							</td> ";
                            echo "</tr>";
                        }

                        ?>

                    </table>

                </div>
                <?php
                if (isset($_GET['approve'])) {
                    if ($_GET['approve'] == 'true') {
                        $hotelId = $_GET['hid'];
                        $premiumRequest = $_GET['premium'];

                        // remove from pending
                        $stmtApprove = $con->prepare("DELETE FROM 
                                                                      pendingHotelAccounts
                                                                WHERE 
                                                                      pendingHotelId = ?");
                        $stmtApprove->execute(array($hotelId));

                        $stmtSetNextPaymentDate = $con->prepare("UPDATE
                                                                            hotel
                                                                           SET
                                                                              nextPaymentDate = ?
                                                                           WHERE
                                                                              hotelId = ?");
                        $oneMonthFromToday = date('Y-m-d', $_SESSION['time'] + 60*60*24*30);
                        $stmtSetNextPaymentDate->execute(array($oneMonthFromToday, $hotelId));
                        if($premiumRequest == '1') {
                            $stmtMakePremium = $con->prepare("INSERT INTO 
                                                                          premiumHotelAccounts(premiumHotelId)
                                                                        VALUES (?)");
                            $stmtMakePremium->execute(array($hotelId));
                        }

                        $_GET['approve'] = 'false';
                        header("Location: broker.php?do=pendingHotels");
                        // todo reload page when hotel presses approve

                    }
                }
                if (isset($_GET['deny'])) {
                    if ($_GET['deny'] == 'true') {
                        $hotelId = $_GET['hid'];
                        $stmtDeny = $con->prepare("DELETE FROM
                                                                hotel
                                                              WHERE hotelId = ?");
                        $stmtDeny->execute(array($hotelId));
                        $_GET['deny'] = 'false';
                        header("Location: broker.php?do=pendingHotels");

                    }
                }
            }
            if ($_GET['do'] == 'allHotels') {

                $today = date('Y-m-d', $_SESSION['time']);

                echo "<div class=\"alert alert-primary\"> Today is ".$today."</div>";
                // view all not pending or suspended hotels
                $stmtAllHotels = $con->prepare("SELECT
                                                                hotel.hotelId, hotel.name, hotel.email, 
                                                                hotel.nextPaymentDate, hotel.moneyDue
                                                           FROM
                                                                hotel LEFT JOIN suspendedHotelAccounts
                                                                ON hotel.hotelID = suspendedHotelAccounts.suspendedHotelId
                                                                LEFT JOIN pendingHotelAccounts
                                                                ON hotel.hotelId = pendingHotelAccounts.pendingHotelId
                                                           WHERE
                                                                suspendedHotelAccounts.suspendedHotelId IS NULL
                                                                AND pendingHotelAccounts.pendingHotelId IS NULL");





                $stmtAllHotels->execute();
                $rows = $stmtAllHotels->fetchAll();

                ?>

                <h1 class="text-center">Hotels</h1>
                <div class="container">
                <div class="table-responsive">
                    <table class="main-table text-center table table-bordered">
                        <tr>
                            <td>Hotel Name</td>
                            <td>Hotel Email</td>
                            <td>Amount Due</td>
                            <td>Due Date</td>
                            <td>Suspend</td>

                        </tr>
                        <?php
                        foreach ($rows as $row) {

                            echo "<tr>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td>" . $row['moneyDue'] . "</td>";
                            echo "<td>" . $row['nextPaymentDate'] . "</td>";
                            echo "<td>
								<a href='broker.php?do=allHotels&suspend=true&hid=" . $row['hotelId'] . "'class='btn btn-danger confirm'>Suspend</a>
							</td> ";
                            echo "</tr>";
                        }

                        ?>

                    </table>

                </div>
                <?php

                if(isset($_GET['suspend'])) {
                    if ($_GET['suspend'] == 'true') {
                        $hotelId = $_GET['hid'];
                        $susQuery = $con->prepare("INSERT INTO suspendedHotelAccounts(suspendedHotelId)
                                                                VALUES (?)");
                        $susQuery->execute(array($hotelId));

                        $_GET['suspend'] = 'false';
                        header("Location: broker.php?do=allHotels");
                    }
                }

            }
            if ($_GET['do'] == 'suspendedHotels') {


                // view all suspended hotels
                $stmtAllHotels = $con->prepare("SELECT
                                                                hotel.hotelId, hotel.name, hotel.email, 
                                                                hotel.nextPaymentDate, hotel.moneyDue
                                                           FROM
                                                                hotel INNER JOIN suspendedHotelAccounts
                                                                ON hotel.hotelId = suspendedHotelAccounts.suspendedHotelId");

                $stmtAllHotels->execute();
                $rows = $stmtAllHotels->fetchAll();

                ?>

                <h1 class="text-center">Hotels</h1>
                <div class="container">
                <div class="table-responsive">
                    <table class="main-table text-center table table-bordered">
                        <tr>
                            <td>Hotel Name</td>
                            <td>Hotel Email</td>
                            <td>Amount Due</td>
                            <td>Due Date</td>
                            <td>Unsuspend</td>

                        </tr>
                        <?php
                        foreach ($rows as $row) {

                            echo "<tr>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td>" . $row['moneyDue'] . "</td>";
                            echo "<td>" . $row['nextPaymentDate'] . "</td>";
                            echo "<td>
								<a href='broker.php?do=suspendedHotels&unSuspend=true&hid=" . $row['hotelId'] . "'class='btn btn-success confirm'>Unsuspend</a>
							</td> ";
                            echo "</tr>";
                        }

                        ?>

                    </table>

                </div>
                <?php

                if(isset($_GET['unSuspend'])) {
                    if ($_GET['unSuspend'] == 'true') {
                        $hotelId = $_GET['hid'];
                        $unSusQuery = $con->prepare("DELETE FROM suspendedHotelAccounts WHERE suspendedHotelId = ?");
                        $unSusQuery->execute(array($hotelId));

                        $_GET['unSuspend'] = 'false';
                        header("Location: broker.php?do=suspendedHotels");
                    }
                }


            }
        }
    }
}?>