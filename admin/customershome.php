<?php
session_start();
$pageTitle= 'Members';
if (isset($_SESSION['Username']) && $_SESSION['type']== 'customer') {
    include 'init.php';
}

