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
    		if($_SESSION['suspended'] == 1){
   				header('Location: sus.php');
   			  	exit();
			}	  
			
			include 'connect.php';
			$tpl='includes/templates/';
			include 'includes/functions/function.php'; 
			include 'includes/languages/english.php';
			?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <title> Results Page</title>
        <link rel="stylesheet" href="layout/css/bootstrap.min.css">
        <link rel="stylesheet" href="layout/css/font-awesome.min.css">
        <link rel="stylesheet" href="layout/css/style.css">
    </head>
    <body >
    	<a href="logout.php">Logout</a>
<?php

			// base query

			/* select rooms in hotels that are not in either pending or suspended 
				left join on premium and order on premium id to show premium first
				left join on reservations to select rooms with no reservations or rooms that have
			\   cancelled reservations rooms that have reservations
				that end before the customer comes or rooms that have reservations that start after the customer leaves
				then concatenate other optional criteria on the query string*/

				// added the conditions that reservations are not cancelled
				// using ? to concatenate to query string before calling prepare(query)

				//1.start date 2.end date 3. start date 3.end date
		
			$query = "
			SELECT 
			       rooms.roomId, hotel.name, hotel.location, hotel.stars, hotel.avgRating, rooms.type, 
			       rooms.price, rooms.offeringHotelId
			FROM hotel
				LEFT JOIN rooms ON rooms.`offeringHotelId` = hotel.`hotelId`
				LEFT JOIN reservations ON reservations.`roomId` = rooms.`roomId`
				LEFT JOIN suspendedHotelAccounts ON suspendedHotelAccounts.`suspendedHotelId` = hotel.hotelId
				LEFT JOIN pendingHotelAccounts ON pendingHotelAccounts.`pendingHotelId` = hotel.hotelId
				LEFT JOIN premiumHotelAccounts ON premiumHotelAccounts.`premiumHotelId` = hotel.hotelId
			WHERE
				suspendedHotelAccounts.suspendedHotelId IS NULL AND
				pendingHotelAccounts.pendingHotelId IS NULL AND
				(reservations.`hotelId` IS NULL OR reservations.isCancelled = 1 OR
					( 
					    (reservations.`fromDate` > ? AND reservations.`fromDate` > ?) OR 
					    (reservations.`toDate` < ? AND reservations.`toDate` < ?) 
					)   
				) 
			";
			$startDateToQuery = $_GET['startDate'];
			$startDateToQuery = strtotime($_GET['startDate']);
			$startDateToQuery = date('Y-m-d', $startDateToQuery);

			$endDateToQuery = $_GET['endDate'];
			$endDateToQuery = strtotime($_GET['endDate']);
			$endDateToQuery = date('Y-m-d', $endDateToQuery);

			$conditions = array($startDateToQuery, $endDateToQuery, $startDateToQuery, $endDateToQuery);

			//concatenate optional conditions
			if(isset($_GET['location']) && $_GET['location'] != "") {
				$query .= " AND hotel.location = ?";
				array_push($conditions, $_GET['location']);
			}
			if(isset($_GET['avgRating'])&& $_GET['avgRating'] != "") {
				$query .= " AND hotel.avgRating > ?";
				array_push($conditions, $_GET['avgRating']);
			}
			if(isset($_GET['stars'])&& $_GET['stars'] != "") {
				$query .= " AND hotel.stars = ?";
				array_push($conditions, $_GET['stars']);
			}
			if(isset($_GET['minPrice'])&& $_GET['minPrice'] != "") {
				$query .= " AND rooms.price >= ?";
				array_push($conditions, $_GET['minPrice']);
			}
			if(isset($_GET['maxPrice'])&& $_GET['maxPrice'] != "") {
				$query .= " AND rooms.price < ?";
				array_push($conditions, $_GET['maxPrice']);
			}
			if(isset($_GET['typeRadio'])&& $_GET['typeRadio'] != "") {
				$query .= " AND rooms.type = ?";
				array_push($conditions, $_GET['typeRadio']);
			}

			//concatenate ordering
			$query .= " ORDER BY premiumHotelAccounts.premiumHotelId DESC";

			$searchStmt = $con->prepare($query);
			$searchStmt->execute($conditions);
			$rows = $searchStmt->fetchAll();

			?>
	
			<h1 class="text-center">Results</h1>
			<div class="container">
				<div class="table-responsive">
					<table class="main-table text-center table table-bordered">
						<tr>
							<td>Hotel</td>
							<td>Location</td>
							<td>Hotel Average Rating</td>
							<td>Stars</td>
							<td>Room Number</td>
							<td>Room Type</td>
							<td>Price</td>
							<td>Make a Reservation</td>

						</tr>
						<?php
						foreach ($rows as $row) {
						  
						echo "<tr>";
							echo 	"<td>" . $row['name'] ."</td>";
							echo 	"<td>" . $row['location'] ."</td>";
							echo 	"<td>" . $row['avgRating'] ."</td>";
							echo 	"<td>" . $row['stars'] ."</td>";
							echo 	"<td>" . $row['roomId'] ."</td>";
							echo 	"<td>" . $row['type'] ."</td>";
							echo 	"<td>" . $row['price'] ."</td>";
 							
							echo	"<td>
								<a href='customer.php?do=registrationSent&offeringHotelId=".$row['offeringHotelId']."
								&roomId=".$row['roomId']."&price=".$row['price']."&hotelName=".$row['name']."
								&reservationStart=".$startDateToQuery."&reservationEnd=".$endDateToQuery."
								' class='btn btn-success'>Make Reservation</a>
							</td> ";
					 	echo "</tr>";
						}
						?>
						
					</table>

</div>
				<?php			

 		}    
	}	
	include "includes/templates/footer.html";	
?>
