<?php
	require('../database.php');
	require('../recServer.php');
	


	header('Content-type: application/json');

	if(isset($_POST))
	{
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


		$recUrl = IP . ':8080/restaurant/recommend/' . $userId;

		
		$json = file_get_contents($recUrl);

		$recs = json_decode($json, true);

		pg_prepare($dbconn,"grab_rid","Select name From restaurant where rid = $1 OR rid = $2 OR rid = $3 OR rid = $4 OR rid = $5 OR rid = $6 OR rid = $7 OR rid = $8 OR rid = $9 OR rid = $10 OR rid = $11 OR rid = $12 OR rid = $13 OR rid = $14 ")
			or die("prepare statement for grabbing rid failed: " . pg_last_error());
		
		$rid = pg_execute($dbconn,"grab_rid",array($recs[0]['itemId'],$recs[1]['itemId'],$recs[2]['itemId'],$recs[3]['itemId'],$recs[4]['itemId'],$recs[5]['itemId'],$recs[6]['itemId'],$recs[7]['itemId'],$recs[8]['itemId'],$recs[9]['itemId'],$recs[10]['itemId'],$recs[11]['itemId'],$recs[12]['itemId'],$recs[13]['itemId']))
			or die("pg_execute for grabbing rids failed: ". pg_last_error());


		$ridArray = pg_fetch_all($rid);
		$google = file_get_contents($url);
		$obj = json_decode($google,true);
	
		$k = 0;
	
		for($i = 0; $i < sizeof($obj['results']); $i++)
		{
			for($j = 0; $j < sizeof($ridArray);$j++)
			{
				if($ridArray[$j]['name'] == $obj['results'][$i]['name'])
				{
				
			 	
					$recNameArray[$k] = $obj['results'][$i]['name'];
					$recLatArray[$k] = $obj['results'][$i]['geometry']['location']['lat'];
					$recLngArray[$k] = $obj['results'][$i]['geometry']['location']['lng'];
				
			
					$geoLat = $recLatArray[$k];
					$geoLng = $recLngArray[$k];
					$geoUrl = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$geoLat,$geoLng&key=$key";		
		
					$geo = file_get_contents($geoUrl);
					$geoObj = json_decode($geo,true);
				
					$address[$k] = $geoObj['results'][0]['formatted_address'];				
					$k++;
					
				}
			}
		}

	
		$uniqueRecNameArray = array_unique($recNameArray);

		$j = 0;
		$finalArray[$j]['success'] = 1;
		for($i = 0; $i < sizeof($recNameArray); $i++)
		{
			if($uniqueRecNameArray[$i] == "")
			{
			}
			else{
				$finalArray[$j+1]['name'] = $uniqueRecNameArray[$i];
				$finalArray[$j+1]['latitude'] = strval($recLatArray[$i]);
				$finalArray[$j+1]['longitude'] = strval($recLngArray[$i]);
		
				$shortAddress = explode(", USA",$address[$i]);
			
				$finalArray[$j+1]['address'] = $shortAddress[0];	
				$j++;
		
		
			}
		}


		$json = json_encode($finalArray);


		echo $json;

	} else {

		
		$arr = array(
			array('success' => 0)
			);
			echo json_encode($arr);	
	}
		


/*

		$arr = array(
			array('success' => 1),
			
	
			array('name' => 'Chipotle', 'latitude' => '123', 'longitude' => '890', 'address' => '306 S 9th St Columbia, MO 65201'),
				
				
			array('name' => 'SubWay', 'latitude' => '000', 'longitude' => '999', 'address' => '1000 St Columbia, MO 65201')
					
		);			
			
*/				
		//if there are no new recommendations
			
	//	$arr = array(
	//		array('success' => 'nonew')
	//
	//	);


	



?>
