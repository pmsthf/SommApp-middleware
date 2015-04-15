<?php
	require('../database.php');
	$dbconn = pg_connect(HOST." ".PORT." ".DBNAME." ".USERNAME." ".PASSWORD);

	header('Content-type: application/json');
//	$myFile = "settings.txt";
//	$fh = fopen($myFile, 'a') or die("can't open file");
	
	if($_POST) {

		$time   = $_POST['time'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		$miles = $_POST['miles'];
		$meters = $miles/0.00062137;
		$meters = round($meters);
	
		if(isset($_POST['name']))
		{
			
			$name = $_POST['name'];
			//grabs firstname field from database where it's tied to the email
			pg_prepare($dbconn,"get_name", "Select firstname from user_info where email = $1")
				or die("PREPARE FOR CHANGE NAME FAILED: " . pg_last_error());
		
			$getName = pg_execute($dbconn,"get_name", array($email))
				or die("EXECUTE STATEMENT FOR CHANGING NAME FAILED: " . pg_last_error());						
			
			$nameArray = pg_fetch_all($getName);
			
			//assings both to lower to check if it's the same name. if it is, do nothing.
			if(strtolower($name) ==  strtolower($nameArray[0]['firstname']))
			{
			}
			//if not the same. assing it all to lower, and make the first letter capital. then update the databae first name that is tied to the email
			else{
			
				pg_prepare($dbconn,"change_name", "Update user_info SET firstname = $1 WHERE email = $2")
					or die("Prepare statement for changing name failed: " . pg_last_error());
				$name = ucfirst(strtolower($name));
		
				$changedName = pg_execute($dbconn, "change_name", array($name,$email))
					or die("EXECUTE STATEMENT FOR CHANGING NAME FAILED: " . pg_last_error());
			}	
		}

		if(isset($_POST['miles']))
		{
			//grabs distance from database, max_distance is in meters
			pg_prepare($dbconn,"get_distance", "Select max_distance from user_info WHERE email = $1")
				or die("Prepare statement for getting miles failed: " . pg_last_error());
			$get_miles = pg_execute($dbconn,"get_distance",array($email))
				or die("Execute statement for getting miles failed: " . pg_last_error());

			$milesArray = pg_fetch_all($get_miles);
		
			//rounds to a whole number and compares
			if($meters == round($milesArray[0]['max_distance']))
			{	
			}
			else
			{
				//if different, changes info in database
				pg_prepare($dbconn, "change_info", "Update user_info SET max_distance = $1 WHERE email = $2 RETURNING max_distance")
					or die("Prepare statement for changing info failed: " . pg_last_error());

				$updated_distance = pg_execute($dbconn, "change_info", array($meters,$email))
					or die("Execute statement for changing info failed: " . pg_last_error());
		
				$distanceArray = pg_fetch_all($updated_distance);			
			}
		}
		if(isset($_POST['password']))
		{
			pg_prepare($dbconn,"get_hash","Select password_hash, salt FROM authentication where email = $1")
				or die("Prepare statement for getting users password failed: " .pg_last_error());
			$passwordResult = pg_execute($dbconn,"get_hash", array($email))
				or die("execute statement for getting users password failed: ". pg_last_error());
			
			$line = pg_fetch_array($passwordResult, null, PGSQL_ASSOC);
			$salt = trim($line['salt']);
			$pwHash = $line['password_hash'];

			$checkHash = SHA1($salt . $password);
			
			if($checkHash == $pwHash)
			{
				//if this is correct then password they asked to change is correct
				// ask connor if he wants an error
			}
			else{
				// add  queries to change password and get new salt and hash	
			}	
		}

		$results =pg_query($dbconn, "Select max_distance from user_info WHERE email='paul@gmail.com'");
		$results = pg_fetch_all($results);



	


		$stringData = "\nTime: ". $time ."\nEmail: " . $email. "\n New name: " . $name. "\nNew password". $password."\n". "New Miles". $miles."\n";
//	fwrite($fh, $stringData);

		echo '{"success":1}';
		
	} else {
		echo '{"success":0}';
		
	}
	//fclose($fh);

?>
