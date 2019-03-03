<?php
session_start();
$pageTitle= 'Members';
if (isset($_SESSION['Username'])) {
	include 'init.php';
	if(isset($_GET["do"]))
	$do=$_GET["do"];
else
	$do="Manage";
if($do=='Manage'){
  ?>
								<a href='members.php?do=Add ' class='btn btn-primary btn-lg btn1'>Add Input </a>
								<a href='members.php?do=Delete ' class='btn btn-primary btn-lg btn2'>View </a> 
								<?php
}
elseif ($do=='Add') {
	?>
	<br/>
	<form method="post"  action="members.php?do=addd">
 <div class="form-group">
    <label class="label1">Number of Members in the family : </label>
    <select class="form-control option1" id="exampleFormControlSelect1" name="email">
      <option value="1">1</option>
      <option value="2">2</option>
      <option value="3">3</option>
      <option value="4">4</option>
    </select>
      <input type="submit" name="formSubmit" value="Next" class="btn btn-primary btn3 btn-lg">
  </div>
</form>
  <?php
}
elseif ($do=='addd') {
	echo $_POST["email"];
}




	include $tpl . 'footer.php';
}
else {
header('Location: index.php');
exit();}




?>