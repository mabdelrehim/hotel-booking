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
        <title> New Hotel Page</title>
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
					$stmtUsername=$con->prepare("SELECT * FROM hotel WHERE username= ? ");   
                    $stmtUsername->execute(array($_POST['username']));
                    $countUsername=$stmtUsername->rowCount(); 
					$rowUsername=$stmtUsername->fetch();

					$stmtEmail=$con->prepare("SELECT * FROM hotel WHERE email= ? ");
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

					}
					

					else {

						// the username and email are unique
						// add a new hotel and add it to the pending hotels
						$stmtInsertHotel=$con->prepare("INSERT INTO hotel(username, email, password, name, location, stars) VALUES
														(?, ?, ?, ?, ?, ?)");   
						$stmtInsertHotel->execute(array($_POST['username'], $_POST['email'], $_POST['password'], $_POST['Name'], $_POST['Location'],
															 $_POST['stars']));
						$lastHotelId = $con->lastInsertId();

						

						//enter hotel facilities
						if(isset($_POST['Wifi'])) {

							$stmtInsertHotel=$con->prepare("INSERT INTO hotelFacilities(hotelId, facility) VALUES (?, ?)");   
							$stmtInsertHotel->execute(array($lastHotelId, $_POST['Wifi']));

						}
						if(isset($_POST['Pool'])) {

							$stmtInsertHotel=$con->prepare("INSERT INTO hotelFacilities(hotelId, facility) VALUES (?, ?)");   
							$stmtInsertHotel->execute(array($lastHotelId, $_POST['Pool']));
							
						}
						if(isset($_POST['Spa'])) {

							$stmtInsertHotel=$con->prepare("INSERT INTO hotelFacilities(hotelId, facility) VALUES (?, ?)");   
							$stmtInsertHotel->execute(array($lastHotelId, $_POST['Spa']));
							
						}
						if(isset($_POST['Gym'])) {
							
							$stmtInsertHotel=$con->prepare("INSERT INTO hotelFacilities(hotelId, facility) VALUES (?, ?)");   
							$stmtInsertHotel->execute(array($lastHotelId, $_POST['Gym']));

						}


						//enter hotel rooms
						for ($i = 0; $i < $_POST['count']; $i++) {
							// insert single rooms
							$type = "single";

							$stmtInsertRoom=$con->prepare("INSERT INTO rooms(offeringHotelId, type, price) VALUES (?, ?, ?)");   
							$stmtInsertRoom->execute(array($lastHotelId, $type, $_POST['price']));

						}

						for ($i = 0; $i < $_POST['count1']; $i++) {
							// insert double rooms
							$type = "double";

							$stmtInsertRoom=$con->prepare("INSERT INTO rooms(offeringHotelId, type, price) VALUES (?, ?, ?)");   
							$stmtInsertRoom->execute(array($lastHotelId, $type, $_POST['price1']));

						}

						for ($i = 0; $i < $_POST['count2']; $i++) {
							// insert single rooms
							$type = "triple";

							$stmtInsertRoom=$con->prepare("INSERT INTO rooms(offeringHotelId, type, price) VALUES (?, ?, ?)");   
							$stmtInsertRoom->execute(array($lastHotelId, $type, $_POST['price2']));

						}

						for ($i = 0; $i < $_POST['count3']; $i++) {
							// insert single rooms
							$type = "royal";

							$stmtInsertRoom=$con->prepare("INSERT INTO rooms(offeringHotelId, type, price) VALUES (?, ?, ?)");   
							$stmtInsertRoom->execute(array($lastHotelId, $type, $_POST['price3']));

						}
						


						// add that hotel to the pending requests
                        if(isset($_POST['Premium'])) {

                            $stmtAddToPendingHotels = $con->prepare("INSERT INTO pendingHotelAccounts(pendingHotelId, premiumRequest) 
                                                                                  VALUES (?, ?)");
                            $stmtAddToPendingHotels->execute(array($lastHotelId, $_POST['Premium']));

                        } else {
                            $stmtAddToPendingHotels = $con->prepare("INSERT INTO pendingHotelAccounts(pendingHotelId) VALUES (?)");
                            $stmtAddToPendingHotels->execute(array($lastHotelId));
                        }

                        header("Location: login.php");


					}


					
					




    		}





    	?>

    	<h1 class="text-center">Add Hotel</h1> 			
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
			<label class="col-sm-2 control-label">Location</label>
			<div class="col-sm-10 col-md-4">
				<input type="text" name="Location" class="form-control" required="required">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Facilities</label>
			<div class="col-sm-10 col-md-4">
				<label class="col-sm-2 control-label">Wifi</label>
				<input type="checkbox" name="Wifi" class="form-control" value="wifi">
				<label class="col-sm-2 control-label">Pool</label>
				<input type="checkbox" name="Pool" class="form-control" value="pool">
				<label class="col-sm-2 control-label">Spa</label>
				<input type="checkbox" name="Spa" class="form-control" value="spa">
				<label class="col-sm-2 control-label">Gym</label>
				<input type="checkbox" name="Gym" class="form-control" value="gym">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Rooms</label> <br/>
			<div class="col-sm-10 col-md-4">
				<label>Single: </label> <br/>
				<label>Count</label>
				<input type="text" name="count" required="required"><br/>
				<label>Price</label>
				<input type="text" name="price" required="required"> <br/>
<br/>

				<label>Double: </label> <br/>
				<label>Count</label>
				<input type="text" name="count1" required="required"><br/>
				<label>Price</label>
				<input type="text" name="price1" required="required"> <br/>
<br/>

				<label>Triple: </label> <br/>
				<label>Count</label>
				<input type="text" name="count2" required="required"><br/>
				<label>Price</label> 
				<input type="text" name="price2" required="required"> <br/>
<br/>
				<label>Royal Suite: </label> <br/>
				<label class=>Count</label>
				<input type="text" name="count3" required="required"> <br/>
				<label class=>Price</label> 
				<input type="text" name="price3" required="required"> <br/><br/>
				<label >Stars: </label> <br/>
				  <input type="range" class="col-sm-1"  name="stars" min="1" max="5" step="1" required="required">
			</div>
		</div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Become Premium</label>
            <div class="col-sm-10 col-md-4">
                <input type="checkbox" name="Premium" class="form-control" value="1">
            </div>
        </div>


		<div class="form-group">
			
			<div class="col-sm-offset-2 col-sm-10">
				<input type="submit" value="Add Hotel" class="btn btn-primary btn-lg">
			</div>
		</div>

	</form>
</div>       



<?php
    include "includes/templates/footer.html";

} ?> 

