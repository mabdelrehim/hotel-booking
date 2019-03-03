<?php
	session_start();
	if(isset($_SESSION['Username'])){ //check if hotel is logged in
		if($_SESSION['Type']=='hotel'){ // if loggedd in make sure its the hotel

			include 'connect.php';
			$tpl='includes/templates/';
			include 'includes/functions/function.php';
			include 'includes/languages/english.php';
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
    <body >
    	<a href="logout.php">Logout</a>
<?php
		$stmt=$con->prepare("SELECT * FROM pendingHotelAccounts WHERE pendingHotelId=? ");   // check if hotel is in pending 
		$stmt->execute(array($_SESSION['id'])); // don't forget to make it hashedpass
		$count=$stmt->rowCount(); 
		$row=$stmt->fetch();
		if($count > 0){ // hotel is in pending
		    // hotel is in pending so redirect to login
            header("Location: login.php");
		} else {  // hotel is not in pending  so check in suspended 
			$stmt=$con->prepare("SELECT * FROM suspendedHotelAccounts WHERE suspendedHotelId=? ");   // check if hotel is in pending 
			$stmt->execute(array($_SESSION['id'])); // don't forget to make it hashedpass
			$count=$stmt->rowCount(); 
			if ($count > 0) {
				echo "<div class=\"alert alert-warning\"> your hotel will not appear in search results until you pay your due fees </div>";
			}
			?>
			<form  action="<?php echo $_SERVER['PHP_SELF'] ?>"  method="POST">
    		  	<input class="form-control" type="text" name="days" placeholder="days" required="required" >
    			<input class="form-control" type="text" name="hours" placeholder="hours" required="required" >
     			 <input class="btn btn-primary btn-block loginn" type="submit" value="Advance Time">
			</form>
			
			
			<a href="hotel.php?do=pendingReservations&hotelId=<?php echo $_SESSION['id']; ?>"><button>View Pending Reservations </button></a>
			<a href="hotel.php?do=allReservations&hotelId=<?php echo $_SESSION['id']; ?>"><button>View All Reservations </button></a>
			<a href="hotel.php?do=reservationsForToday&hotelId=<?php echo $_SESSION['id']; ?>"><button>View Reservations for Today</button></a>
			<a href="hotel.php?do=allCheckIns&hotelId=<?php echo $_SESSION['id']; ?>"><button>View all Check Ins</button></a>
			<a href="hotel.php?do=pay&hotelId=<?php echo $_SESSION['id']; ?>"><button>Pay Monthly Fees</button></a> <!--todo-->
			<?php
			if($_SERVER['REQUEST_METHOD']=='POST'){
				$days = $_POST['days'];
				$hours = $_POST['hours'];
				$_SESSION['time'] = strtotime("today + ". $days ."days" . " + " . $hours ." hours");
			}
			if(isset($_GET['do'])){
				/* if($_GET['do']=='checkin'){
					echo ("check in test");
					echo ($_SESSION['time']);
					$stmt=$con->prepare("SELECT * FROM checkins WHERE checkInDate=? AND hotelId=?");   // check if hotel is in pending 
					$stmt->execute(array( date("m/d/Y",strtotime("today + ". $days ."days" . " + " . $hours ." hours")),$_SESSION['id'])); 
					$count=$stmt->rowCount(); 
					$row=$stmt->fetch(); 
				} */
				
				if($_GET['do'] == 'allCheckIns') {
					$stmt= $con->prepare("SELECT * FROM checkIns WHERE hotelId=?");
					$stmt->execute(array($_SESSION['id']));
					$rows=$stmt->fetchAll();
					
					?>
	
			<h1 class="text-center">All Check Ins</h1>
			<div class="container">
				<div class="table-responsive">
					<table class="main-table text-center table table-bordered">
						<tr>
							<td>Customer Name</td>
							<td>Check In Date</td>
							<td>Check Out Date</td>	
							<td>Amount Spent</td>						
						</tr>
						<?php
						foreach ($rows as $row) {

							$stmtGetCustomerName= $con->prepare("SELECT * FROM customer WHERE customerId=?");
							$stmtGetCustomerName->execute(array($row['customerId']));
							$rowsCustomerName=$stmtGetCustomerName->fetchAll();
						 
						  
						echo "<tr>";
							echo 	"<td>" . $rowsCustomerName[0]['name'] ."</td>";
							echo 	"<td>" . $row['checkInDate'] ."</td>";
							echo 	"<td>" . $row['checkOutDate'] ."</td>";
							echo 	"<td>" . $row['amountPayed'] ."</td>";
 							
							/* echo	"<td>
								<a href='managers.php?do=Edit&userid=".$row['UserID'] ."' class='btn btn-success'>Edit </a>
								<a href='managers.php?do=Delete&userid=".$row['UserID'] ."'class='btn btn-danger confirm'>Delete </a>
							</td> "; */
					 	echo "</tr>";
						}
						?>
						
					</table>

</div>
				<?php			
				}
				elseif ($_GET['do']=='pay') {
				    // payment is due every 30 days starting from when the hotel was approved

                    //get last previous next payment date
                    $stmtGetPay = $con->prepare("SELECT nextPaymentDate FROM hotel WHERE hotelId = ?");
                    $stmtGetPay->execute(array($_SESSION['id']));
                    $row = $stmtGetPay->fetch();
                    $lastNextPaymentDate = $row['nextPaymentDate'];

				    // pay and set next payment date to one month from today
					
					$stmtPay = $con->prepare("UPDATE hotel SET moneyDue = 0, nextPaymentDate = ?
                                                        WHERE hotelId = ?");



					$oneMonthFromLastDate = date('Y-m-d', strtotime($lastNextPaymentDate) + 60*60*24*30);
					$stmtPay->execute(array($oneMonthFromLastDate, $_SESSION['id']));
                    echo "<div class=\"alert alert-success\">Payed successfully. If your hotel was previously suspended, it will reappear in search results.</div>";

					// unsuspend if suspended
                    $stmtCheck=$con->prepare("SELECT * FROM suspendedHotelAccounts WHERE suspendedHotelId=? ");
                    $stmtCheck->execute(array($_SESSION['id'])); // don't forget to make it hashedpass
                    $countCheck=$stmtCheck->rowCount();
                    if ($count > 0) {
                        $stmtUnsuspend = $con->prepare("DELETE FROM suspendedHotelAccounts 
                                                                      WHERE suspendedHotelId = ?");
                        $stmtUnsuspend->execute(array($_SESSION['id']));
                    }


				}
				elseif ($_GET['do']=='pendingReservations') {
					
					
					$stmt= $con->prepare("SELECT 
                                                      customer.name, reservations.reservationId, reservations.roomId,
                                                      reservations.fromDate, reservations.toDate, reservations.customerId,
                                                      customer.numberOfReservationsMade
                                                    FROM 
                                                      reservations INNER JOIN pendingReservation 
                                                      ON reservations.reservationId = pendingReservation.pendingReservationId 
                                                      INNER JOIN customer ON customer.customerId = reservations.customerId 
                                                    WHERE 
                                                      hotelId=? AND isCancelled=0");
					$stmt->execute(array($_SESSION['id']));
					$rows=$stmt->fetchAll();
					
					?>
	
			<h1 class="text-center">Pending Reservation Requests</h1>
			<div class="container">
				<div class="table-responsive">
					<table class="main-table text-center table table-bordered">
						<tr>
							<td>Customer Name</td>
							<td>Room Number</td>
							<td>From</td>
							<td>To</td>
							<td>Approve/Cancel</td>

						</tr>
						<?php
						foreach ($rows as $row) {

						echo "<tr>";
							echo 	"<td>" . $row['name'] ."</td>";
							echo 	"<td>" . $row['roomId'] ."</td>";
							echo 	"<td>" . $row['fromDate'] ."</td>";
							echo 	"<td>" . $row['toDate'] ."</td>";
 							
							echo	"<td>
								<a href='hotel.php?do=pendingReservations&approve=true&rid=".$row['reservationId'] ."&cid=".$row['customerId']."&num=".$row['numberOfReservationsMade']."' class='btn btn-success'>Approve</a>
								<a href='hotel.php?do=pendingReservations&cancel=true&rid=".$row['reservationId'] ."'class='btn btn-danger confirm'>Cancel</a>
							</td> ";
					 	echo "</tr>";
						}
						?>
						
					</table>

</div>
						<?php
						if(isset($_GET['approve'])) {
							if($_GET['approve'] == 'true') {
								$rid = $_GET['rid'];
								$stmtRemovePending = $con->prepare("DELETE FROM pendingReservation WHERE pendingReservationId = ?");
								$stmtRemovePending->execute(array($rid));

								$stmtApprove = $con->prepare("UPDATE reservations SET isApproved = 1 WHERE reservationId = ?");
								$stmtApprove->execute(array($rid));

								$stmtIncreaseNumReservations = $con->prepare("UPDATE customer SET numberOfReservationsMade = ?
                                                                                        WHERE customerId = ?");

								$newNumberOfReservationsMade = $_GET['num'] + 1;
								$stmtIncreaseNumReservations->execute(array($newNumberOfReservationsMade, $_GET['cid']));

								$_GET['approve'] = 'false';
								header("Location: hotel.php?do=pendingReservations");
								// todo reload page when hotel presses approve

							}
						}	
						if(isset($_GET['cancel'])) {
							if($_GET['cancel'] == 'true') {

								$rid = $_GET['rid'];
								$stmtRemovePending = $con->prepare("DELETE FROM reservations WHERE reservationId = ?");
								$stmtRemovePending->execute(array($reservationId));
								$_GET['cancel'] = 'false';
                                header("Location: hotel.php?do=pendingReservations");

							}
						}					


				}	
				elseif ($_GET['do']=='allReservations') {
					$stmt= $con->prepare("SELECT 
                                                      customer.name, reservations.reservationId, reservations.roomId,
                                                      reservations.fromDate, reservations.toDate 
                                                    FROM 
                                                      reservations INNER JOIN customer ON reservations.customerId = customer.customerId
                                                    WHERE 
                                                      hotelId=? AND isApproved=1");
					$stmt->execute(array($_SESSION['id']));
					$rows=$stmt->fetchAll();
					
					?>
	
			<h1 class="text-center">All Reservations</h1>
			<div class="container">
				<div class="table-responsive">
					<table class="main-table text-center table table-bordered">
						<tr>
							<td>Customer Name</td>
							<td>Room Number</td>
							<td>From</td>
							<td>To</td>							
						</tr>
						<?php
						foreach ($rows as $row) {
						  
						echo "<tr>";
							echo 	"<td>" . $row['name'] ."</td>";
							echo 	"<td>" . $row['roomId'] ."</td>";
							echo 	"<td>" . $row['fromDate'] ."</td>";
							echo 	"<td>" . $row['toDate'] ."</td>";
 							
							/* echo	"<td>
								<a href='managers.php?do=Edit&userid=".$row['UserID'] ."' class='btn btn-success'>Edit </a>
								<a href='managers.php?do=Delete&userid=".$row['UserID'] ."'class='btn btn-danger confirm'>Delete </a>
							</td> "; */
					 	echo "</tr>";
						}
						?>
						
					</table>

</div>
						<?php
						// todo refresh page			
				}
				elseif ($_GET['do']=='reservationsForToday') {
					
					$stmt= $con->prepare("SELECT reservations.fromDate, reservations.toDate, reservations.roomId, reservations.totalPrice, reservations.customerId, reservations.reservationId 
												FROM reservations LEFT JOIN checkIns ON reservations.reservationId = checkIns.reservationId 
												WHERE reservations.hotelId=? AND reservations.fromDate=? AND reservations.isCancelled=0 
												AND reservations.isApproved=1 AND checkIns.reservationId IS NULL");
					$stmt->execute(array($_SESSION['id'], date("Y-m-d", $_SESSION['time']))); ///heyyyyyyyy ana 3adelt hena yarab matekhrab
					$rows=$stmt->fetchAll();
					
					?>
	
			<h1 class="text-center">Reservations for Today</h1>
			<div class="container">
				<div class="table-responsive">
					<table class="main-table text-center table table-bordered">
						<tr>
							<td>Customer Name</td>
							<td>Room Number</td>
							<td>From</td>
							<td>To</td>
							<td>Amount to Pay</td>
							<td>Check In</td>							
						</tr>
						<?php
						foreach ($rows as $row) {

							$stmtGetCustomerName= $con->prepare("SELECT * FROM customer WHERE customerId=?");
							$stmtGetCustomerName->execute(array($row['customerId']));
							$rowsCustomerName=$stmtGetCustomerName->fetchAll();
						 
						  
						echo "<tr>";
							echo 	"<td>" . $rowsCustomerName[0]['name'] ."</td>";
							echo 	"<td>" . $row['roomId'] ."</td>";
							echo 	"<td>" . $row['fromDate'] ."</td>";
							echo 	"<td>" . $row['toDate'] ."</td>";
							echo 	"<td>" . $row['totalPrice'] ."</td>";
							echo	"<td>
								<a href='hotel.php?do=reservationsForToday&addCheckIn=true&reservationId=".$row['reservationId'] ."' class='btn btn-success'>Check In</a>
							</td> ";
 							
							/* echo	"<td>
								<a href='managers.php?do=Edit&userid=".$row['UserID'] ."' class='btn btn-success'>Edit </a>
								<a href='managers.php?do=Delete&userid=".$row['UserID'] ."'class='btn btn-danger confirm'>Delete </a>
							</td> "; */
					 	echo "</tr>";
						}
						?>
						
					</table>

</div>
						<?php
					if(isset($_GET['addCheckIn'])) {
						if($_GET['addCheckIn']=='true') {
							if(isset($_GET['reservationId'])) {
								$reservationId = $_GET['reservationId'];
								$stmtGetReservation= $con->prepare("SELECT * FROM reservations WHERE reservationId=?");
								$stmtGetReservation->execute(array($reservationId));
								$rowsReservation=$stmtGetReservation->fetchAll();

								

								$stmtInsertCheckIn = $con->prepare("INSERT INTO checkIns(reservationId, customerId, hotelId, checkInDate, checkOutDate, amountPayed) 
																		VALUES(?, ?, ?, ?, ?, ?)");
								$stmtInsertCheckIn->execute(array($reservationId, $rowsReservation[0]['customerId'], $rowsReservation[0]['hotelId'], $rowsReservation[0]['fromDate'],
																			$rowsReservation[0]['toDate'], $rowsReservation[0]['totalPrice']));

								$stmtGetMoneyDue = $con->prepare("SELECT * FROM hotel WHERE hotelId=?");
								$stmtGetMoneyDue->execute(array($rowsReservation[0]['hotelId']));
								$rowsMoneyDue=$stmtGetMoneyDue->fetchAll();
								$newMoneyDue = $rowsMoneyDue[0]['moneyDue'] + ($rowsReservation[0]['totalPrice']*0.09);

								
								$stmtUpdateMoneyDue = $con->prepare("UPDATE hotel SET moneyDue = ? WHERE hotelId = ?");
								$stmtUpdateMoneyDue->execute(array($newMoneyDue, $rowsReservation[0]['hotelId']));
								$_GET['addCheckIn'] = 'false';
                                header("Location: hotel.php?do=reservationsForToday");
							}
						}		
					}						
				}				
					
			}			

		}

	}
}

include "includes/templates/footer.html";
?>