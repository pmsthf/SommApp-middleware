<?php

	require('../database.php');

	$dbconn = pg_connect(HOST." ".PORT." ".DBNAME." ".USERNAME." ".PASSWORD);

	$password = $_POST['password'];
	$email = $_POST['email'];
	$firstname = $_POST['name'];
	$max_distance = 1609;
	mt_srand();
	$salt = mt_rand();

	$pwhash = SHA1($salt . $password);	


	pg_prepare($dbconn, "grab_username","Select email FROM user_info WHERE email = $1") or die ("Select statement for username failed: " . pg_last_error());
	$selectName = pg_execute($dbconn, "grab_username",array($email)) or die("Select execute for username failed: ". pg_last_error());

	$rows = pg_num_rows($selectName);
	
	if($rows > 0)
	{
		echo '{"success":0,"error_message":"Username Exists."}';
	}
	else{

	//user info
		pg_prepare($dbconn, "user_query","INSERT INTO user_info(firstname,email,registration_date,max_distance) VALUES($1,$2, DEFAULT, $3)") or die ("User prepare statement failed: ". pg_last_error());
		$userInfoResult = pg_execute($dbconn,"user_query", array($firstname, $email,$max_distance)) or die("User Query Execute Failed: ". pg_last_error());

		//authentication
		pg_prepare($dbconn,"authentication_query","INSERT INTO authentication(email,password_hash,salt) VALUES($1,$2,$3)")
		or die("Prepare Authentication Failed: ". pg_last_error());

		$authResult = pg_execute($dbconn,"authentication_query",array($email,$pwhash,$salt)) or die("Authentication Execute Failed: " .pg_last_error());

		
		echo '{"success":1}';
	}
?>
