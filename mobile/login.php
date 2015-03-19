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
			echo '{"success":1}';
		}
		else
		{
			echo '{"success":0, "error_message":"Invalid Password"}';
		}




	}




?>
