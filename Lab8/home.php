<?php

	//checks if user is using https
	if($_SERVER["HTTPS"] != "on"){
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	}
	else{
		session_start();
		
		//checks if there is a session
		if($_SESSION['count'] != 1){
			header('Location: index.php');
		}
		else{
			//connects to database
			include("../secure/database.php");
			$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD)
				or die('Could not connect: ' . pg_last_error());
		
			//gets info for display
			$query = "SELECT username, registration_date, description FROM lab8.user_info WHERE username = $1";
			pg_prepare($conn, "user_info", $query);
			$result = pg_execute($conn, "user_info", array($_SESSION["username"]));
			$line = pg_fetch_array($result, null, PGSQL_ASSOC);
			
			//gets ip for display
			$query = "SELECT ip_address FROM lab8.log WHERE action = $1";
			pg_prepare($conn, "user_ip", $query);
			$result1 = pg_execute($conn, "user_ip", array('register'));
			$line1 = pg_fetch_array($result1, null, PGSQL_ASSOC);
			
			//gets user's log
			$query = "SELECT action, ip_address, log_date FROM lab8.log WHERE username = $1 ORDER BY log_date DESC";
			pg_prepare($conn, "user_log", $query);
			$result2 = pg_execute($conn, "user_log", array($_SESSION["username"]));
		
			$numRows = pg_num_rows($result2);
			
			//info to send to update or logout
			$_SESSION['username'] = $line["username"];
			$_SESSION['count'] = 1;
			$_SESSION['desc'] = $line["description"];
			
			//prints info
			echo '
			<div align="center">
			<p>Username: ' . $line["username"] . '</p>
			<p>Ip Address: ' . $line1['ip_address'] . '</p>
			<p>Registration Date: ' . $line["registration_date"] . '</p>
			<p>Description: ' . $line["description"] . '</p>';
			
			// Prints the resulting table
			echo "<p>There were <i>" . $numRows . "</i> rows returned.</p>";
			echo "<table border=1>\n";
			//Prints the column headers
			echo "\t<tr>\n";
			for($i = 0; $i < pg_num_fields($result2); $i++){
				echo "\t\t<td align='center'><strong>" . pg_field_name($result2, $i) . "<strong></td>\n";
			}
			echo "\t</tr>\n";
			//Prints the results
			while ($line = pg_fetch_array($result2, null, PGSQL_ASSOC)) {
				echo "\t<tr>\n";
				foreach ($line as $col_value) {
					echo "\t\t<td>$col_value</td>\n";
				}
				echo "\t</tr>\n";
			}
			echo "</table>\n
			<p>Click <a href='update.php'>here</a> to update page.</p>
			<br>
			<p><a href='logout.php'>Click here to logout</a></p>
			</div>";
			
			// Free resultset
			pg_free_result($result);
			pg_free_result($result1);
			pg_free_result($result2);
			
			//Closes the connection
			pg_close($conn);
		}
	}
?>