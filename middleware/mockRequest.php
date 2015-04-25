<?php
	header('Content-type: application/json');
	$myFile = "rec.txt";
	$fh = fopen($myFile, 'a') or die("can't open file");

	if($_POST) {
		$time   = $_POST['time'];
		$email = $_POST['email'];
		$coords = $_POST['coords'];
		$stringData = "\nTime: ". $time ."\nEmail: " . $email. "\n Coords: " . $coords. "\n";
		fwrite($fh, $stringData);

		$arr = array(
			array('success' => '1'),
			array('name' => 'Chipotle', 'latitude' => '38.94808', 'longitude' => '-92.3274487', 'address' => '306 S 9th St Columbia, MO 65201'),
			array('name' => 'Taco Bell', 'latitude' => '38.946758', 'longitude' => '-92.334858', 'address' => '411 S Providence Rd Columbia, MO 65203'),
			array('name' => 'Main Squeeze', 'latitude' => '38.950661', 'longitude' => '-92.327368', 'address' => '28 S 9th St Columbia, MO 65201'),
			array('name' => 'Lakota Coffee Company', 'latitude' => '38.950661', 'longitude' => '-92.327368', 'address' => '24 S 9th St Columbia, MO 65201'),
			array('name' => 'Kaldis Coffee House', 'latitude' => '38.950653', 'longitude' => '-92.327732', 'address' => '29 S 9th St #1 Columbia, MO 65201'),
			array('name' => 'Ingredient', 'latitude' => '38.9481684', 'longitude' => '-92.3272968', 'address' => '304 S 9th St Columbia, MO 65201'),
			array('name' => 'Shakespeares Pizza', 'latitude' => '38.948610', 'longitude' => '-92.327792', 'address' => '225 S 9th St Columbia, MO 65201'),
			array('name' => 'Yogoluv', 'latitude' => '38.949337', 'longitude' => '-92.327888', 'address' => '201 S 9th St Columbia, MO 65201')
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
