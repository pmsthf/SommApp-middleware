<?php

	require('../database.php');
	require('../recServer.php');


	header('Content-type:application/json');
	
	$name = $_POST['restaurantName'];

	$dbconn = pg_connect(HOST." ".PORT." ".DBNAME." ".USERNAME." ".PASSWORD);


	pg_prepare($dbconn, "get_rid","Select rid FROM Restaurant WHERE name = $1")
		or die("PREPARE STATEMENT FOR getting rid failed: " . pg_last_error());

	$results = pg_execute($dbconn,"get_rid",array($name))
		or die("Execute statement for getting rid failed: " . pg_last_error());

	$info = pg_fetch_all($results);
	$rid = $info[0]['rid'];

	$aUrl = IP . ':8080/restaurant/analytics/'. $rid;
		
	$json = file_get_contents($aUrl);
	$anal = json_decode($json,true);

	print_r($json);

	
/*	echo "<BR><BR><BR>";
	//echo $anal[0]['restaurantId'];


	echo "<BR><BR><BR>";
	echo json_encode($anal);

	//echo "<BR><BR><BR>". $analytics[0]['restaurantId'];

*/
         /* $arr = array(
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
*/
?>
