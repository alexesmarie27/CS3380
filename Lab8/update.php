<?php

	//checks if user is using https
	if($_SERVER["HTTPS"] != "on"){
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	}
	else{
		session_start();
		
		//if there is no session, movee to index.php
		if(!$_SESSION['count'] == 1){
			header("Location: index.php");
		}
		else{
			echo '
			<!DOCTYPE html>
			<html>
			<head>
			<title>CS 3380 Lab 8</title>
			</head>
			<body>
			<form method="POST" action="/~ampwd6/cs3380/lab8/update.php">
			<div align="center">
				<p>Username: ' . $_SESSION["username"] . '</p>
				<table border="1">
				<tr>
					<td><strong>Description</strong></td><td><input type="text" name="description" value="'.$_SESSION["desc"].'"/></td>
				</tr></table>
				<input type="submit" name="submit" value="Save"/>
				<p><a href="logout.php">Click here to logout</a></p>
			</div>
			</form>
			</body>
			</html>';
		}
		
		if(isset($_POST["submit"])){
			
			//connect to database
			include("../secure/database.php");
			$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD)
				or die('Could not connect: ' . pg_last_error());
				
			//get the new description
			$_SESSION["desc"] = htmlspecialchars($_POST['description']);
			
			//insert update into user's log
			$query = "INSERT INTO lab8.log (username, ip_address, log_date, action) VALUES ($1, $2, DEFAULT, 'updated description')";
			pg_prepare($conn, "update_log", $query);
			$result = pg_execute($conn, "update_log", array($_SESSION["username"], $_SERVER["REMOTE_ADDR"]));
			
			//update the information
			$query = "UPDATE lab8.user_info SET description = $1 WHERE username = $2";
			pg_prepare($conn, "update", $query);
			$result = pg_execute($conn, "update", array($_SESSION["desc"], $_SESSION["username"]));
			
			// Free resultset
			pg_free_result($result);
			
			//Closes the connection
			pg_close($conn);
			
			header("Location: home.php");
		}
	}
?>