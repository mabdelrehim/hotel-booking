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
        <title> Hotel Page</title>
        <link rel="stylesheet" href="layout/css/bootstrap.min.css">
        <link rel="stylesheet" href="layout/css/font-awesome.min.css">
        <link rel="stylesheet" href="layout/css/style.css">
    </head>
    <body>
    	<?php
    		if($_SERVER['REQUEST_METHOD'] == 'POST'){
					// queries logic
					$errorsBool = false;
					$stmtAvailableHotels=$con->prepare("
					SELECT rooms.roomId, hotel.name, hotel.location, hotel.stars, hotel.avgRating, rooms.type, rooms.price 
					FROM hotel
    					LEFT JOIN rooms ON rooms.`offeringHotelId` = hotel.`hotelId`
    					LEFT JOIN reservations ON reservations.`roomId` = rooms.`roomId`
    					LEFT JOIN suspendedHotelAccounts ON suspendedHotelAccounts.`suspendedHotelId` = hotel.hotelId
    					LEFT JOIN pendingHotelAccounts ON pendingHotelAccounts.`pendingHotelId` = hotel.hotelId
					WHERE
						suspendedHotelAccounts.suspendedHotelId IS NULL AND
						pendingHotelAccounts.pendingHotelId IS NULL AND
						reservations.`hotelId` IS NULL OR (
						(reservations.`fromDate` > :startDate AND reservations.`fromDate` > :endDate) OR (
						 reservations.`toDate` < :startDate AND reservations.`toDate` < :endDate) ) 
						 AND hotel.`location` = :location;
					");
							
					if(isset($_POST['startDate'])) {
						$stmtAvailableHotels->bindValue(':startDate', $_POST['startDate'], PDO::PARAM_STR);
					}
					if(isset($_POST['endDate'])) {
						$stmtAvailableHotels->bindValue(':endDate', $_POST['endDate'], PDO::PARAM_STR);
					}
					if(isset($_POST['location'])) {
						$stmtAvailableHotels->bindValue(':location', $_POST['location'], PDO::PARAM_STR);
					}

					$stmtAvailableHotels->execute();

					while($row = $stmtAvailableHotels->fetch(PDO::FETCH_ASSOC)) {
						$hotelName = htmlentities($row['name']);
						$hotelStars = htmlentities($row['stars']);
						$roomType = htmlentities($row['type']);
						$roomPrice = htmlentities($row['price']);

						echo $hotelName . ' ' . $hotelStars . ' ' . $roomType . ' ' . $roomPrice . '<br />';
					}

			}
		?>		
<div class="container">
	<form class="form-horizontal" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<label for="location">Location:</label>
		<input type="text" class="location" name="location">
		<label for="start">Start date:</label>
		<input type="date" id="start" name="startDate"
       		value="2018-12-22"
       		min="2018-01-01" max="2018-12-31">
		<input type="date" id="end" name="endDate"
       		value="2018-12-26"
       		min="2018-01-01" max="2019-12-12">

		<input type="submit" value="Find Hotels" class="btn btn-primary btn-lg">

	</form>
</div>       



<?php
	include "includes/templates/footer.html";
	}	
?> 
