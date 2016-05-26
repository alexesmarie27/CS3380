<!DOCTYPE html>
<html>
<head>
	<title>CS 3380 Lab 3</title>
</head>
<body>
	<form method="POST" action="/~ampwd6/cs3380/lab3/lab3.php">
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
			<option value="10" >Query 10</option>
			<option value="11" >Query 11</option>
			<option value="12" >Query 12</option>
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
				$query = "SELECT district, population FROM lab3.city WHERE name='Springfield' ORDER BY population DESC;";
				break;
			case 2:
				$query = "SELECT name, district, population FROM lab3.city WHERE country_code='BRA' ORDER BY name ASC;";
				break;
			case 3:
				$query = 'SELECT name, continent, surface_area FROM lab3.country ORDER BY surface_area ASC LIMIT 20;';
				break;
			case 4:
				$query = 'SELECT name, continent, government_form, gnp FROM lab3.country WHERE gnp > 200000 ORDER BY name ASC;';
				break;
			case 5:
				$query = 'SELECT name, life_expectancy FROM lab3.country WHERE life_expectancy IS NOT NULL ORDER BY life_expectancy DESC OFFSET 10 LIMIT 10;';
				break;
			case 6:
				$query = "SELECT name FROM lab3.city WHERE name LIKE 'B%' AND name LIKE '%s' ORDER BY population DESC;";
				break;
			case 7:
				$query = 'SELECT city.name AS name, country.name AS country, city.population FROM lab3.city INNER JOIN lab3.country USING (country_code) WHERE city.population > 6000000 ORDER BY city.population DESC';
				break;
			case 8:
				$query = 'SELECT co.name, cl.language, cl.percentage FROM lab3.country_language AS cl INNER JOIN lab3.country AS co USING (country_code) WHERE is_official=false AND population >50000000 ORDER BY cl.percentage DESC;';
				break;
			case 9:
				$query = "SELECT co.name, co.indep_year, co.region FROM lab3.country AS co INNER JOIN lab3.country_language AS cl USING (country_code) WHERE cl.language='English' AND cl.is_official=true ORDER BY co.region ASC, co.name ASC;";
				break;
			case 10:
				$query = 'SELECT DISTINCT ct.name AS capital_name, co.name AS country_name,((ct.population*100)/co.population) AS urabn_pct FROM lab3.country AS co INNER JOIN lab3.city AS ct ON (co.capital = ct.id) ORDER BY urabn_pct DESC;';
				break;
			case 11:
				$query = 'SELECT co.name, cl.language,round(cl.percentage*co.population/100) AS speakers FROM lab3.country_language AS cl INNER JOIN lab3.country AS co USING (country_code) WHERE is_official=true ORDER BY speakers DESC;';
				break;
			case 12:
				$query = 'SELECT name, region, gnp, gnp_old,((gnp - gnp_old)/gnp_old) AS real_change FROM lab3.country WHERE gnp IS NOT NULL AND gnp_old IS NOT NULL ORDER BY real_change DESC;';
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
