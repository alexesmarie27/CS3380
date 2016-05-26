<!DOCTYPE html>
<html>
<head>
	<title>CS 3380 Lab 4</title>
</head>
<body>
	<form method="POST" action="/~ampwd6/cs3380/lab4/lab4.php">
		<select name="query">
			<option value="1" >Query 1</option>
			<option value="2" >Query 2</option>
			<option value="3" >Query 3</option>
			<option value="4" >Query 4</option>
			<option value="5" >Query 5</option>
			<option value="6" >Query 6</option>
			<option value="7" >Query 7</option>
			<option value="8" >Query 8</option>
			<option value="9" >Query 9</option>
		</select>
		<input type="submit" name="submit" value="Execute" />
	</form>
	<br />
	<hr />
	<br />
<?php
	//Checks if the submit button was pressed
	if(!isset($_POST['submit'])){
		echo '<p><strong>Select a query from the above list.<strong></p>';
	}
	else{
		// Connects to the database
		include("../secure/database.php");
		$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD)
			or die('Could not connect: ' . pg_last_error());

		//Determines the query the user is selecting
		switch ($_POST["query"]) {
			case 1:
				$query = "CREATE OR REPLACE VIEW weight AS SELECT pid, fname, lname FROM lab4.person AS pn INNER JOIN lab4.body_composition AS bc USING (pid) WHERE bc.weight > 140; SELECT * FROM weight;";
				break;
			case 2:
				$query = "CREATE OR REPLACE VIEW BMI AS SELECT wt.fname, wt.lname, round(703*bc.weight/(pow(bc.height,2))) AS bmi FROM weight AS wt INNER JOIN lab4.body_composition AS bc USING (pid) WHERE bc.weight>150; SELECT * FROM BMI";
				break;
			case 3:
				$query = "SELECT university_name, city FROM lab4.university AS uni WHERE NOT EXISTS (SELECT * FROM lab4.person AS pn WHERE uni.uid = pn.uid);";
				break;
			case 4:
				$query = "SELECT fname, lname FROM lab4.person AS pn WHERE uid IN (SELECT uid FROM lab4.university AS uni WHERE city = 'Columbia');";
				break;
			case 5:
				$query = "SELECT activity_name FROM lab4.activity AS act WHERE activity_name NOT IN (SELECT activity_name FROM lab4.participated_in AS part);";
				break;
			case 6:
				$query = "SELECT pid FROM lab4.participated_in AS part WHERE activity_name = 'running' UNION SELECT pid FROM lab4.participated_in AS part WHERE activity_name = 'racquetball';";
				break;
			case 7:
				$query = "SELECT pn.fname, pn.lname FROM lab4.person AS pn INNER JOIN lab4.body_composition AS bc USING (pid) WHERE bc.age > 30 INTERSECT SELECT pn.fname, pn.lname FROM lab4.person AS pn INNER JOIN lab4.body_composition AS bc USING (pid) WHERE bc.height >65;";
				break;
			case 8:
				$query = "SELECT pn.fname, pn.lname, bc.weight, bc.height, bc.age FROM lab4.person AS pn INNER JOIN lab4.body_composition AS bc USING (pid) ORDER BY bc.height DESC, bc.weight ASC, pn.lname ASC;";
				break;
			case 9:
				$query = "WITH students AS (SELECT pn.pid, pn.fname, pn.lname FROM lab4.person AS pn WHERE pn.uid =2) SELECT * FROM students INNER JOIN lab4.body_composition USING (pid);";
				break;
		}

		$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		$numRows = pg_num_rows($result);

		// Prints the resulting table
		echo "<p>There were <i>" . $numRows . "</i> rows returned.</p>";
		echo "<table border=1>\n";
		//Prints the column headers
		echo "\t<tr>\n";
		for($i = 0; $i < pg_num_fields($result); $i++){
			echo "\t\t<td align='center'><strong>" . pg_field_name($result, $i) . "<strong></td>\n";
		}
		echo "\t</tr>\n";
		//Prints the results
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			echo "\t<tr>\n";
			foreach ($line as $col_value) {
				echo "\t\t<td>$col_value</td>\n";
			}
			echo "\t</tr>\n";
		}
		echo "</table>\n";

		// Free resultset
		pg_free_result($result);

		//Closes the connection
		pg_close($conn);
	}
?>
</body>
</html>
