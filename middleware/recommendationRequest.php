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
			array('name' => 'Chipotle', 'latitude' => 2, 'longitude' => 3, 'address' => 'a'),
			array('name' => 'TacoBell', 'latitude' => 2, 'longitude' => 3, 'address' => 'a')

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
