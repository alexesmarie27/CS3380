<?php 

	//checks if user is using https
	if($_SERVER["HTTPS"] != "on"){
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	}
	else{
		session_start();
		
		//if user is logged in
		if($_SESSION['count'] == 1){
			header("Location: home.php");
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
					<p>Please login</p>
					<form action="/~ampwd6/cs3380/lab8/index.php" method="post">
						<label for="username">Username:</label>
						<input type="text" name="username" id="username">
						<label for="password">Password:</label>
						<input type="password" name="password" id="password">
						<br><br>
						<input type="submit" name="submit" value="submit">
					</form> 
					<p>Register <a href="registration.php">here</a></p>
				</div>
			</body>
			</html>';
		}

		if(isset($_POST["submit"])){
		  
			//Connects to database
			include("../secure/database.php");
			$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD)
				or die('Could not connect: ' . pg_last_error());
			
			//Collects salt from table
			$query = 'SELECT salt FROM lab8.authentication WHERE (username = $1)';
			pg_prepare($conn, "salt", $query);
			$result = pg_execute($conn, "salt", array($_POST["username"]));
			
			$username = $_POST["username"];
			$password = $_POST["password"];
			$line = pg_fetch_array($result, null, PGSQL_ASSOC);
			$salt = $line["salt"];	
			$pass_hash = sha1($password . $salt);

			//Checks if user is in table
			$query = 'SELECT * FROM lab8.authentication WHERE (username = $1) AND (password_hash = $2)';
			pg_prepare($conn, "authentication", $query);
			$result = pg_execute($conn, "authentication", array($username, $pass_hash));
			$numRows = pg_num_rows($result);

			if($numRows == 0){ //if login is unsuccessful
				echo '<div align="center"><p>Please Enter Your Login Information Again</p></div>';
			}
			else{ //if login is successful
						
				//puts login info into log table
				$query = "INSERT INTO lab8.log (username, ip_address, log_date, action) VALUES ($1, $2, DEFAULT, 'login')";
				pg_prepare($conn, "login_log", $query);
				$result = pg_execute($conn, "login_log", array($_POST["username"], $_SERVER["REMOTE_ADDR"]));
			
				// Free resultset
				pg_free_result($result);
					
				//Closes the connection
				pg_close($conn);
			
				//Info for home.php
				$_SESSION['username'] = $username;
				$_SESSION['count'] = 1;
				
				header("Location: home.php");
				exit();
			}
		} 
	}
?>