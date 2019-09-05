<?php
require_once("assets/includes/route.php");

	// 1. start the session ..
	@session_start();
	// 2. destroy session vars ..
	$_SESSION = array();


	// 3. destroy session cookie ..
	if (isset($_COOKIE[session_name()])) {
	setcookie(session_name() , '' , time()-42000 , '/');		
	}


	// 4. destroy the session ..
	session_destroy();
	
	redirect_to($url_mapper['login/'].'logout=true');
?>