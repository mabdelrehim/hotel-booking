<?php 
function lang($phrase){
 static $lang = array(


 	'home_admin' => 'Home',
 	'Categories_admin' => "Categories",
 	'edit_profile_admin' => "Edit Profile",
 	'Settings_admin' => "Settings",
 	'Logout_admin' => "Logout",
 	'Items'=>'Items',
 	'Members'=>'Members',
 	'Statistics'=>'Statistics',
 	'Logs'=>'Logs',


 	 );
return $lang[$phrase];

}
?>