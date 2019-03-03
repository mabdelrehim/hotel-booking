<?php

/* Title function that echo the page title in case the page has the variable $pagetitle or echos default for other pages */

function getTitle(){
	global $pageTitle;
if(isset($pageTitle)){
	echo $pageTitle;
}
else 
	echo "Default";
}
?>