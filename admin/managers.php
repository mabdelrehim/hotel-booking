<?php
session_start();
$pageTitle= 'Managers';
if (isset($_SESSION['Username'])) {
	include 'init.php';
	if(isset($_GET["do"]))
	$do=$_GET["do"];
else
	$do="Manage";
if($do=='Manage'){
	$stmt= $con->prepare("SELECT * FROM users WHERE GroupID = 0");
	$stmt->execute();
	$rows=$stmt->fetchAll();
	?>
	
			<h1 class="text-center">Manage Managers</h1>
			<div class="container">
				<div class="table-responsive">
					<table class="main-table text-center table table-bordered">
						<tr>
							<td>#ID</td>
							<td>Username</td>							
							<td>Full Name</td>
							
							<td>Control</td>
						</tr>
						<?php
						foreach ($rows as $row) {
						 
						  
						echo "<tr>";
							echo 	"<td>" . $row['UserID'] ."</td>";
							echo 	"<td>" . $row['Username'] ."</td>";
							echo 	"<td>" . $row['FullName'] ."</td>";
 							
							echo	"<td>
								<a href='managers.php?do=Edit&userid=".$row['UserID'] ."' class='btn btn-success'>Edit </a>
								<a href='managers.php?do=Delete&userid=".$row['UserID'] ."'class='btn btn-danger confirm'>Delete </a>
							</td> ";
					 	echo "</tr>";
						}
						?>
						
					</table>

						<a href="managers.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i>Add New Manager</a>
</div>
						<?php
	 
}
elseif ($do=="Add") { ?>

<h1 class="text-center">Add Manager</h1>
<div class="container">
	<form class="form-horizontal" method="POST" action="?do=Insert">
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
				<i class="show-pass fa fa-eye fa-2x"></i>

			</div>
		</div>
	
		<div class="form-group">
			<label class="col-sm-2 control-label">Full Name</label>
			<div class="col-sm-10 col-md-4">
				<input type="text" name="FullName" class="form-control" >
			</div>
		</div>
		<div class="form-group">
			
			<div class="col-sm-offset-2 col-sm-10">
				<input type="submit" value="Add Manager" class="btn btn-primary btn-lg">
			</div>
		</div>
	</form>
</div>
<?php }

elseif ($do=="Insert") {
	echo $do;
	if($_SERVER['REQUEST_METHOD']=="POST"){
		echo "<h1 class='text-center'>Insert Page</h1>";
		echo "<div class='container'>";
			$username=$_POST['username'];
			$FullName=$_POST['FullName'];
			$password=$_POST['password'];
			$hashedpass=sha1($password);
			$errors= array();
			// validation
			if(strlen($username)<4){
				$errors[]="<div class='alert alert-danger'>Username cannot be less than 4 characters </div>";

			}
			if(empty($username)){
				$errors[]="<div class='alert alert-danger'> Username cannot be empty </div>";
			}
			if(empty($password)){
				$errors[]="<div class='alert alert-danger'> Password cannot be empty </div>";
			}
			if(empty($FullName)){
				$errors[]="<div class='alert alert-danger'> Full Name cannot be empty </div>";
			}
			foreach ($errors as $error) {
			echo $error . "<br/>";		
				}echo "</div>";
				if(empty($errors)){

					$stmt = $con->prepare("INSERT INTO 
													users(Username, Password, FullName)
												VALUES(? ,?,  ?) ");
						$stmt-> execute(array(
							 $username,
							$hashedpass,
							
							 $FullName
							
						));
						
					echo "<div class='alert alert-success'>" . $stmt->rowCount() . " Records Updated</div>";
				}
				
	}
	else{
		echo "You can't Browse This page insert";
		}
		}
elseif ($do=="Edit"){
$userid= isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0 ;

$stmt=$con->prepare("SELECT * FROM users WHERE UserID=?");
$stmt->execute(array($userid));
$count=$stmt->rowCount();
$row=$stmt->fetch();
if($count > 0){
	?>


<h1 class="text-center">Edit Manager</h1>
<div class="container">
	<form class="form-horizontal" method="POST" action="?do=Update">
		<input type="hidden" name="userid" value="<?php echo $userid; ?>">
		<div class="form-group">
			<label class="col-sm-2 control-label">Username</label>
			<div class="col-sm-10 col-md-4">
				<input type="text" name="username" class="form-control" value="<?php echo $row["Username"] ?>" required="required">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Password</label>
			<div class="col-sm-10 col-md-4">
				<input type="Password" name="password" class="form-control">
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-sm-2 control-label">Full Name</label>
			<div class="col-sm-10 col-md-4">
				<input type="text" name="FullName" class="form-control" value="<?php echo $row["FullName"] ?>">
			</div>
		</div>
		<div class="form-group">
			
			<div class="col-sm-offset-2 col-sm-10">
				<input type="submit" value="Save" class="btn btn-primary btn-lg">
			</div>
		</div>
	</form>
</div>

<?php
} 

else {
	echo "There is no such id".$userid;
}
/*

*/
}
elseif ($do == "Delete"){
$userid= isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0 ;

$stmt=$con->prepare("SELECT * FROM users WHERE UserID=?");
$stmt->execute(array($userid));
$count=$stmt->rowCount();
if($count > 0){
	$stmt= $con->prepare("DELETE FROM users where UserID=?");
	$stmt->execute( array($userid));
	echo "<div class='alert alert-success'>" . $stmt->rowCount() . " Records Deleted</div>";

}
else
echo "User doesn't exist";}
elseif ($do == "Update") {
	echo "<h1 class='text-center'>Update Page</h1>";
	echo "<div class='container'>";
	if($_SERVER['REQUEST_METHOD']=="POST"){
			$username=$_POST['username'];
			$FullName=$_POST['FullName'];
			$userid=$_POST['userid'];
			$password=$_POST['password'];
			$errors= array();
			// validation
			if(strlen($username)<4){
				$errors[]="<div class='alert alert-danger'>Username cannot be less than 4 characters </div>";

			}
			if(empty($username)){
				$errors[]="<div class='alert alert-danger'> Username cannot be empty </div>";
			}
			if(empty($FullName)){
				$errors[]="<div class='alert alert-danger'> Full Name cannot be empty </div>";
			}
			
			foreach ($errors as $error) {
			echo $error . "<br/>";		
				}
				if(empty($errors)){
			// password trick
			if(empty($password)){
			
				$stmt=$con->prepare("UPDATE users SET Username=?,FullName=? WHERE UserID=?");
				$stmt->execute(array($username,$FullName,$userid));
				echo "<div class='alert alert-success'>" . $stmt->rowCount() . " Records Updated</div>";
			}
			else
			{
			$hashedpass=sha1($password);
			$stmt=$con->prepare("UPDATE users SET Username=?,FullName=?,Password=? WHERE UserID=?");
			$stmt->execute(array($username,$FullName,$hashedpass,$userid));
							echo "<div class='alert alert-success'>" . $stmt->rowCount() . " Records Updated</div>";

			}}
	}
	else{
		echo "You can't Browse update";
		}
		echo "</div>";
}

include $tpl . 'footer.php';
}
else {
header('Location: index.php');
exit();}




?>