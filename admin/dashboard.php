<?php
session_start();
$pageTitle= 'Dashboard';
if (isset($_SESSION['Username'])) {
	include 'init.php';
include $tpl . 'footer.php';
header('Location: managers.php');
exit();
}
else {
header('Location: index.php');
exit();
}
