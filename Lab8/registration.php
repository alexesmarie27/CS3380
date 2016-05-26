<?php

	//checks if user is usin https
	if($_SERVER["HTTPS"] != "on"){
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	}
	else{
		echo '
		<!DOCTYPE html>
		<html>
		<head>
		<title>CS 3380 Lab 8</title>
		</head>
		<body>
			<div align = "center">               
				<p>Please register</p>
				<form action="/~ampwd6/cs3380/lab8/registration.php" method="post">
					<label for="username">Username:</label>
					<input type="text" name="username" id="username">
					<label for="password">Password:</label>
					<input type="password" name="password" id="password">
					<br><br>
					<input type="submit" name="submit" value="submit">
				</form> 
			</div>
		</body>
		</html>';
		
		if(isset($_POST["submit"])){
			//Connects to database
			include("../secure/database.php");
			$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD)
				or die('Could not connect: ' . pg_last_error());
		  
			//gets the hashed random number as salt, and hashes the password and salt together
			$username = htmlspecialchars($_POST["username"]);
			$pass = htmlspecialchars($_POST["password"]);
			mt_srand();
			$randnum = mt_rand();
			$salt = sha1($randnum);
			$SaltedPassword = $pass . $salt;
			$pass_hash = sha1($SaltedPassword);
			
			//Adds a row to the user_info table
			$query  = 'INSERT INTO lab8.user_info (username) VALUES ($1)';
			pg_prepare($conn, 'user_info', $query);
			$result = pg_execute($conn, 'user_info', array($username));
			
			//if there is an error
			if (pg_last_error() != false){
				echo '<div align="center"><p>Please Enter Your Login Information Again</p>
				<p>Return to <a href="index.php">login page</a></p>
				<p>Return to <a href="registration.php">registration page</a></p></div>';
			}
			else{
				//adds log in log table
				$query = "INSERT INTO lab8.log (username, ip_address, log_date, action) VALUES ($1, $2, DEFAULT, 'register')";
				pg_prepare($conn, "register_log", $query);
				$result = pg_execute($conn, "register_log", array($username, $_SERVER["REMOTE_ADDR"]));
					
				//Adds a row to the authentication table
				$query = 'INSERT INTO lab8.authentication VALUES ($1, $2, $3)';
				pg_prepare($conn, "authentication", $query);
				$result = pg_execute($conn, "authentication", array($username, $pass_hash, $salt));
				
				//Checks if successful
				$query = 'SELECT * FROM lab8.authentication WHERE username = $1';
				pg_prepare($conn, "success", $query);
				$result = pg_execute($conn, "success", array($username)); 
				$numRows = pg_num_rows($result);
				
				//if not successfully added
				if($numRows == 0){
					echo '<div align="center"><p>Please Enter Your Login Information Again</p></div>
					<p>Return to registration <a href="registration.php">page</a></p>';
				}
				else{ //if successfully added
				
					// Free resultset
					pg_free_result($result);
						
					//Closes the connection
					pg_close($conn);
					
					//Starts a session
					session_start();
					$_SESSION['count'] = 1;
					$_SESSION['username'] = $username;
					
					//moves user to home.php
					header('Location: home.php');
				}
			}
		}
	}
?>