<?php 
include 'connect.php';
$tpl='includes/templates/';
$lang= 'includes/languages/';
include 'includes/functions/function.php'; 
include $lang .'english.php';
include $tpl . 'header.php';
if(!isset($noNavbar)){

include $tpl . 'navbar.php';


}

?>