<?php
	session_start();
	
	//checks if there is currently a session
	if($_SESSION['count'] == 1){
	
		//connects to database
		include("../secure/database.php");
		$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD)
			or die('Could not connect: ' . pg_last_error());
		
		//insert into user's log
		$query = "INSERT INTO lab8.log (username, ip_address, log_date, action) VALUES ($1, $2, DEFAULT, 'logout')";
		pg_prepare($conn, "logout", $query);
		$result = pg_execute($conn, "logout", array($_SESSION["username"], $_SERVER["REMOTE_ADDR"]));
				
		// Free resultset
		pg_free_result($result);
		
		//Closes the connection
		pg_close($conn);
		
		//ends the session
		session_destroy();
		
		header('Location: index.php');
	}
	else
		header('Location: index.php');
?>