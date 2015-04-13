<?php
	header('Content-type: application/json');
	$myFile = "reccomendation.txt";
	$fh = fopen($myFile, 'a') or die("can't open file");

	if($_POST) {
		$time   = $_POST['time'];
		$email = $_POST['email'];
		$coords = $_POST['coords'];
		$stringData = "\nTime: ". $time ."\nEmail: " . $email. "\n Coords: " . $coords. "\n";
		fwrite($fh, $stringData);



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

?>
