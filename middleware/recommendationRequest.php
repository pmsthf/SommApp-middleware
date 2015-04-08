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

		echo '{"success":1}';
		
	} else {
		echo '{"success":0}';
		
	}
	fclose($fh);

?>
