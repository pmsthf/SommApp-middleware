<?php
	require('../database.php');
	require('../recServer.php');
	


	header('Content-type: application/json');


		$dbconn = pg_connect(HOST." ".PORT." ".DBNAME." ".USERNAME." ".PASSWORD);	
		
		$key = "AIzaSyCmM8yC1X_fOgLqv5TV2nPaXxgPuBGyRmc";
		
		$email = $_POST['email'];
		$gps = $_POST['gps'];
		$coords = explode(",",$gps);
		$lat = $coords[0];
		$lng = $coords[1];


		pg_prepare($dbconn, "grab_user_info", "Select user_id, max_distance FROM user_info WHERE email = $1")
			or die("PREPARE STATEMENT for grabbing distance failed: " . pg_last_error());
		$result = pg_execute($dbconn,"grab_user_info",array($email))
			or die("Execute statement for grabbing distance failed: " . pg_last_error());


		$info = pg_fetch_all($result);
		$meters = $info[0]['max_distance'];	
		$url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$lat,$lng&radius=$meters&rankBy=distance&types=restaurant&openNow=true&key=$key";
			
		$userId = $info[0]['user_id'];





		$recUrl = IP . ':8080/restaurant/recommendation/' . $userId;

		
		$json = file_get_contents($recUrl);
		$recs = json_decode($json);
		print_r($recs);
		

/*
		$arr = array(
			array('success' => 1),
			array('name' => 'Chipotle', 'latitude' => '38.94808', 'longitude' => '-92.3274487', 'address' => '306 S 9th St Columbia, MO 65201'),
			array('name' => 'TacoBell', 'latitude' => '38.934833', 'longitude' => '-92.3315709', 'address' => '411 S Providence Rd Columbia, MO 65203')

			);
			
		//if there are no new recommendations
			
		$arr = array(
			array('success' => 'nonew')
			);

		echo json_encode($arr);


	} else {

		
		$arr = array(
			array('success' => 0)
			);
			
					echo json_encode($arr);

	
	}
	fclose($fh);
*/
?>
