<?php
//Connects to the database
include("../secure/database.php");
$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die('Could not connect: ' . pg_last_error());

//If inserting a new city into the table, user enters name, country_code, district, and population
if($_GET["action"] == "insert"){
	if(!isset($_POST['submit'])){

		$query = "SELECT name, country_code FROM lab5.country ORDER BY name;";
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());

		//displays a form for inserting a new city
		echo "
		<!DOCTYPE html>
		<html>
		<head>
		<title>CS 3380 Lab 5</title>
		</head>
		<body>
		<form method='POST' action='/~ampwd6/cs3380/lab5/exec.php?action=insert'>
		<input type='hidden' name='action' value='save_insert'/>
		Enter data for the city to be added: <br/>
		<table border=1>
		<tr><td>Name</td><td><input type='text' name='name'/></td></tr>
		<tr><td>Country Code</td><td><select name='country_code'>\n";

		//provides a drop down list of countries
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			echo "\t\t\t<option value='". $line["country_code"] ."'>" . $line["name"] . "</option>\n";
		}

		$link = '"index.php"';
		echo "
		</select></td></tr>
		<tr><td>District</td><td><input type='text' name='district'/></td></tr>
		<tr><td>Population</td><td><input type='text' name='population'/></td></tr>
		</table>
		<input type='submit' name='submit' value='Save'/>
		<input type='button' value='Cancel' onclick='top.location.href=$link;'/>
		</form>
		</body>
		</html>\n";
	}
	else{
		$result = pg_prepare($conn, "add_city", "INSERT INTO lab5.city VALUES (DEFAULT, $1, $2, $3, $4);");
		$result = pg_execute($conn, "add_city", array($_POST["name"], $_POST["country_code"], $_POST["district"], $_POST["population"]));

		echo "
		<p>Insert was successful</br>
		Return to <a href='index.php'>search page</a></p>\n";
	}
}
else if($_POST["actions"] == "Edit"){
	if(!isset($_POST['submit'])){
		if($_POST["value"] == "country"){
			$result = pg_prepare($conn, "country_edit1", "SELECT * FROM lab5.country WHERE country_code = $1");
			$result = pg_execute($conn, "country_edit1", array(htmlspecialchars($_POST["id"])));
		}
		else if($_POST["value"] == "city"){
			$result = pg_prepare($conn, "city_edit1", "SELECT * FROM lab5.city WHERE id = $1");
			$result = pg_execute($conn, "city_edit1", array(htmlspecialchars($_POST["id"])));
		}
		else{
			$result = pg_prepare($conn, "language_edit1", "SELECT * FROM lab5.country_language WHERE language = $1 AND country_code = $2");
			$result = pg_execute($conn, "language_edit1", array(htmlspecialchars($_POST["id"]), htmlspecialchars($_POST["country"])));
		}

		//returns the number of rows
		$numRows = pg_num_rows($result);

		//prints the table for the selected
		echo "
		<!DOCTYPE html>
		<html>
		<head>
		<title>CS 3380 Lab 5</title>
		</head>
		<body>
		<form method='POST' action='/~ampwd6/cs3380/lab5/exec.php'>
		<table border=1>\n";

		while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){

			//if updating country, provide textboxes for local name, government form, population, and indep year
			if($_POST["value"] == "country"){
				$j=0;
				foreach ($line as $col_value){
					$field = pg_field_name($result, $j);
					echo "\t\t<tr>\n";
					if($field == "local_name" || $field == "government_form" || $field == "population" || $field == "indep_year"){
						echo "
						<td><strong>" . $field . "<strong></td>\n
						<td><input type='text' name='" . $field . "' value='" . htmlspecialchars($col_value) . "'></td>\n";
					}
					else{
						echo "
						<td>" . $field . "</td>\n
						<td>". $col_value . "</td>\n";
					}
					echo "\t\t</tr>\n";
					$j++;
				}
			}
			//if updating city, provide textboxes for district and population
			else if ($_POST["value"] == "city"){
				$j=0;
				foreach ($line as $col_value){
					$field = pg_field_name($result, $j);
					echo "\t\t<tr>\n";
					if($field == "population" || $field == "district"){
						echo "
						<td><strong>" . $field . "<strong></td>\n
						<td><input type='text' name='" . $field . "' value='" . htmlspecialchars($col_value) . "'></td>\n";
					}
					else{
						echo "
						<td>" . $field . "</td>\n
						<td>". $col_value . "</td>\n";
					}
					echo "\t\t</tr>\n";
					$j++;
				}
			}
			//If updating a language, provide textboxes for is official and percentage
			else{
				$j=0;
				foreach ($line as $col_value){
					$field = pg_field_name($result, $j);
					echo "\t\t<tr>\n";
					if($field == "is_official" || $field == "percentage"){
						echo "
						<td><strong>" . $field . "<strong></td>\n
						<td><input type='text' name='" . $field . "' value='" . htmlspecialchars($col_value) . "'></td>\n";
					}
					else{
						echo "
						<td>" . $field . "</td>\n
						<td>". $col_value . "</td>\n";
					}
					echo "\t\t</tr>\n";
					$j++;
				}

				echo "<input type='hidden' name='country' value=" . $_POST['country'] . ">\n";
			}
		}

		$link = '"index.php"';
		echo "
		</table>
		<input type='hidden' name='actions' value='Edit'>
		<input type='hidden' name='id' value='" . $_POST["id"] . "'>
		<input type='hidden' name='value' value='" . $_POST["value"] . "'>
		<input type='submit' name='submit' value='Save'/>
		<input type='button' value='Cancel' onclick='top.location.href=$link;'/>
		</form>
		</body>
		</html>\n";
	}
	else{
		//updates the local name, government form, population, and indep year for each country
		if($_POST["value"] == "country"){
			$result = pg_prepare($conn, "country_edit2", "UPDATE lab5.country SET local_name = $1, government_form = $2, indep_year = $3, population = $4 WHERE country_code = $5");
			$result = pg_execute($conn, "country_edit2", array($_POST["local_name"], $_POST["government_form"], $_POST["indep_year"], $_POST["population"], $_POST["id"]));
		}
		//updates the population and district for each country
		else if($_POST["value"] == "city"){
			$result = pg_prepare($conn, "city_edit2", "UPDATE lab5.city SET population = $1, district = $2 WHERE id = $3");
			$result = pg_execute($conn, "city_edit2", array($_POST["population"], $_POST["district"], $_POST["id"]));
		}
		//updates the is_official and population for each language
		else{
			$result = pg_prepare($conn, "language_edit2", "UPDATE lab5.country_language SET is_official = $1, percentage = $2 WHERE language = $3 AND country_code = $4");
			$result = pg_execute($conn, "language_edit2", array($_POST["is_official"], $_POST["percentage"], $_POST["id"], $_POST["country"]));
		}

		//success message
		echo "
		<p>Edit was successful</br>
		Return to <a href='index.php'>search page</a></p>";
	}
}
//if actions == delete, determines what to delete from what table
else{
	//if deleting a country, finds the row with the similar country code and deletes it from the table
	if($_POST["value"] == "country"){
		$result = pg_prepare($conn, "country_delete", "DELETE FROM lab5.country WHERE country_code = $1");
		$result = pg_execute($conn, "country_delete", array($_POST["id"]));
	}
	//If deleting a city, find the row with the similar id and deletes it from the table
	else if($_POST["value"] == "city"){
		$result = pg_prepare($conn, "city_delete", "DELETE FROM lab5.city WHERE id = $1");
		$result = pg_execute($conn, "city_delete", array($_POST["id"]));
	}
	//if deleting a language, finds the row with the name and country_code of that language and deletes it from the table
	else{
		$result = pg_prepare($conn, "language_delete", "DELETE FROM lab5.country_language AS cl WHERE cl.language = $1 AND cl.country_code = $2");
		$result = pg_execute($conn, "language_delete", array($_POST["id"], $_POST["country"]));
	}

	//success message
	echo "
	Delete was successful </br>
	Return to <a href='index.php'>search page</a>\n";
}

//Free resultset
pg_free_result($result);

//Closes the connection
pg_close($conn);
?>
