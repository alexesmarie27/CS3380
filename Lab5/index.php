<!DOCTYPE html>
<html>
<head>
	<title>CS 3380 Lab 5</title>
</head>
<body>
	<form method="POST" action="/~ampwd6/cs3380/lab5/index.php">
		Search for a :
		<input type="radio" name="search_by" checked="true" value="country"/>Country
		<input type="radio" name="search_by" value="city"/>City
		<input type="radio" name="search_by" value="language"/>Language
		<br /><br />
		That begins with:
		<input type="text" name="search_string" value=""/>
		<br /><br />
		<input type="submit" name="submit" value="Submit"/>
	</form>
	<hr/>
	Or insert a new city by clicking this <a href="exec.php?action=insert">link</a>

<?php
	if(isset($_POST['submit'])){
		//Connects to the database
		include("../secure/database.php");
		$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die('Could not connect: ' . pg_last_error());

		//searches the database for schema matching the user input
		switch ($_POST["search_by"]){
			case "country":
				$result = pg_prepare($conn, "country_search", "SELECT * FROM lab5.country WHERE name ILIKE $1 ORDER BY name");
				$result = pg_execute($conn, "country_search", array($_POST["search_string"]."%"));
				break;
			case "city":
				$result = pg_prepare($conn, "city_search", "SELECT * FROM lab5.city WHERE name ILIKE $1 ORDER BY name");
				$result = pg_execute($conn, "city_search", array($_POST["search_string"]."%"));
				break;
			case "language":
				$result = pg_prepare($conn, "language_search", "SELECT * FROM lab5.country_language WHERE language ILIKE $1 ORDER BY language");
				$result = pg_execute($conn, "language_search", array($_POST["search_string"]."%"));
				break;
		}

		$numRows = pg_num_rows($result);

		// Prints the resulting table
		echo "<p>There were <i>" . $numRows . "</i> rows returned.</p>";
		echo "<table border=1>\n";
		//Prints the column headers
		echo "\t<tr>\n";
		for($i = 0; $i < pg_num_fields($result) + 1; $i++){
			if($i == 0)
				echo "\t\t<td align='center'><strong>Actions<strong></td>\n";
			else
				echo "\t\t<td align='center'><strong>" . pg_field_name($result, $i-1) . "<strong></td>\n";
		}
		echo "\t</tr>\n";
		//Prints the results
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			echo "	\t<tr>\n
					<td><form method='POST' action='/~ampwd6/cs3380/lab5/exec.php'>";

					//sets the id in POST to the primary key of the row for the edit form
					if($_POST["search_by"] == "country"){
						echo "
						<input type='hidden' name='id' value='". $line["country_code"] ."'>";
					}
					else if($_POST["search_by"] == "city"){
						echo "
						<input type='hidden' name='id' value='". $line["id"] ."'>";
					}
					else{
						echo "
						<input type='hidden' name='id' value='". $line["language"] ."'>
						<input type='hidden' name='country' value='". $line["country_code"] ."'>";
					}

					//They all have this in common. Value is set to country, city, or language
					echo "
						<input type='hidden' name='value' value='". $_POST["search_by"] ."'>
						<input type='submit' name='actions' value='Edit'/>
						</form>
						<form method='POST' action='/~ampwd6/cs3380/lab5/exec.php'>";

					//id is set for the delete form
					if($_POST["search_by"] == "country"){
						echo "
						<input type='hidden' name='id' value='". $line["country_code"] ."'>";
					}
					else if($_POST["search_by"] == "city"){
						echo "
						<input type='hidden' name='id' value='". $line["id"] ."'>";
					}
					else{
						echo "
						<input type='hidden' name='id' value='". $line["language"] ."'>
						<input type='hidden' name='country' value='". $line["country_code"] ."'>";
					}

					//They all have this in common. value is set to country, city, or language
					echo "
						<input type='hidden' name='value' value='". $_POST["search_by"] ."'>
						<input type='submit' name='actions' value='Remove'/>
						</form></td>";

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
