<?php

	require('../database.php');
	require('../recServer.php');


	$dbconn = pg_connect(HOST." ".PORT." ".DBNAME." ".USERNAME." ".PASSWORD);	

	$key = "AIzaSyCmM8yC1X_fOgLqv5TV2nPaXxgPuBGyRmc";
	$gps = $_POST['gps'];
	$email = $_POST['email'];
	$miles = $_POST['miles'];	
	$coords = explode(",",$gps);
	$start_time = $_POST['start_time'];
	$end_time = $_POST['end_time'];

	$duration = strtotime($end_time) - strtotime($start_time);

	$lat =$coords[0];
	$lng = $coords[1];

	$meters = $miles/0.00062137;
	$meters = round($meters);	
	
	$url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$lat,$lng&radius=$meters&rankBy=distance&types=restaurant&openNow=true&key=$key";
	
	$i = 0;

	$json = file_get_contents($url);
	$obj = json_decode($json,true);

	$restaurants = pg_query($dbconn, "Select rid, name FROM Restaurant");
	$restArr = pg_fetch_all($restaurants);


		for( $i = 0; $i < sizeof($obj['results']); $i++)
		{
			
			$restLat = $obj['results'][$i]['geometry']['location']['lat'];
			$restLng = $obj['results'][$i]['geometry']['location']['lng'];
			$restName = $obj['results'][$i]['name'];
			$placeID = $obj['results'][$i]['place_id'];	
		
			$inDB = false;
			for($j = 0; $j < sizeof($restArr); $j++)
			{
				if($restArr[$j]['name'] == $restName)
				{
					$inDB = true;
								
				}
	
			}
			if($inDB == false)
			{

				pg_prepare($dbconn, "insertRestaurant_$i", "INSERT INTO Restaurant(rid,name) VALUES(DEFAULT,$1)") 
						or die("Prepare for inserting into restaurant table failed: ". pg_last_error());
	
				$insertRestResult = pg_execute($dbconn,"insertRestaurant_$i", array($restName))
						or die ("Insert Execute into restaurant table failed: ". pg_last_error());
			
				pg_prepare($dbconn,"grab_rid_$i", "Select rid FROM Restaurant WHERE name = $1")
						or die("Select statement checking if restaurant is already in the database: ". pg_last_error());
	
				$ridSearch = pg_execute($dbconn,"grab_rid_$i", array($restName))
						or die("Select execute for restaurant check failed: ". pg_last_error());
						

				$ridArray = pg_fetch_array($ridSearch, 0, PGSQL_NUM);
				$rid = reset($ridArray);

				pg_prepare($dbconn,"insertPlace_$i", "INSERT INTO Place(pid,rid,loc) VALUES ($1,$2,ST_GeomFromText($3))")
					or die("Prepare for insertPlace failed: ". pg_last_error());
		
				$geom = "POINT(" . $restLat. " ". $restLng. ")', 4326)";
		
				pg_execute($dbconn,"insertPlace_$i",array($placeID,$rid,$geom)) 		
					or die("Insert execute for Place table failed: ". pg_last_error());
   
						
			}
			else
			{

				// add queries to check if the restaurant location doesnt already exist in the place database nd if it does add to it.
				
			}



		



	
		}// end of for loop





	for($i =0; $i< sizeof($restArr);$i++)
	{
		if($obj['results'][0]['name'] == $restArr[$i]['name'])
		{
			pg_prepare($dbconn,"grab_rest_rid","SELECT rid FROM Restaurant WHERE name = $1")
				or die("PREPARE STATEMENT FOR GRABBING RID FAILED: " . pg_last_error());
			$restRidResult = pg_execute($dbconn,"grab_rest_rid", array($obj['results'][0]['name']))
				or die("EXECUTE STATEMENT FOR GRABBING RID FAILED: " . pg_last_error());

			$restRidArray = pg_fetch_array($restRidResult, 0, PGSQL_NUM);
       			$rid = reset($restRidArray);
			
			pg_prepare($dbconn,"grab_user_id","SELECT user_id FROM user_info WHERE email = $1")
				or die("PREPARE STATEMENT FOR GRABBING user id FAILED: " . pg_last_error());
			$userIdResult = pg_execute($dbconn,"grab_user_id", array($email))
				or die("EXECUTE STATEMENT FOR GRABBING user id FAILED: " . pg_last_error());

			$userIdArray = pg_fetch_array($userIdResult, 0, PGSQL_NUM);
       		        $userid = reset($userIdArray);
			
			$recommendationUrl = IP . ':8080/visit/restaurant/' . $userId . '/' . $rid . '/' . $duration;
			$opts = array('http' => 
				array(
					'method' => 'POST', 
					'header' => 'Content-type: application/x-www-form-urlencoded'
			));

			$context = stream_context_create($opts);
			$result = file_get_contents($recommendaitonUrl, false, $context);
	

		}
	}

	

	echo "<BR>REST ID: " . $rid;

	echo "<BR>USER ID: " . $userid;

	echo "<BR>DURATION: " . $duration;


?>

