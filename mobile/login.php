<?php
	
	require('../database.php');

	$dbconn = pg_connect(HOST." ".PORT." ".DBNAME." ".USERNAME." ".PASSWORD);

	$email = $_POST['email'];
	$password = $_POST['password'];


	pg_prepare($dbconn,"check_user", "Select email From user_info Where email = $1") or die("Checking using prepare statement failed: " . pg_last_error());

	$userResult = pg_execute($dbconn,"check_user", array($email)) or die("Execute for checking user failed: " . pg_last_error());
	
	$rows = pg_num_rows($userResult);

	if($rows == 0)
	{
		echo '{"success":0,"error_message":"Invalid Username."}';
	}
	else
	{
		
		pg_prepare($dbconn,"get_hash","Select password_hash, salt FROM authentication WHERE email = $1")
			or die("Prepare statement for getting users password failed: " .pg_last_error());

		$passwordResult = pg_execute($dbconn,"get_hash", array($email)) or die("Execute statement for getting users password failed: " . pg_last_error());

		$line = pg_fetch_array($passwordResult, null, PGSQL_ASSOC);

		$salt = trim($line['salt']);
		$pwHash = $line['password_hash'];

		$checkHash = SHA1($salt . $password);
		
		if($checkHash == $pwHash)
		{
			pg_prepare($dbconn,"get_firstname", "Select firstname FROM user_info where email = $1")
				or die("Prepare statement for getting user first name failed: " . pg_last_error());
			$firstnameResult = pg_execute($dbconn,"get_firstname", array($email)) or die("Execte statement failed getting first name: " . pg_last_error());
			$line = pg_fetch_array($firstnameResult, null, PGSQL_ASSOC);
			$firstname = $line['firstname'];
			$firstname = json_encode($line['firstname']);

			$maxMiles = 1
			//also need to return the maximum miles this user wants restaurants from (needs to be set to default # in database)


			echo '{"success":1, "firstname":'.$firstname.'."maxMiles":'.$maxMiles.'}';
					
		}
		else
		{
			echo '{"success":0, "error_message":"Invalid Password"}';
		}




	}




?>
