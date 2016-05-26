<!DOCTYPE html>
<html>
<head>
	<title>CS 3380 Lab 6</title>
</head>
<body>
	<form method="POST" action="/~ampwd6/cs3380/lab6/lab6.php">
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
		</select>
		<input type="submit" name="submit" value="Execute" />
	</form>
	<br />
	<hr />
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
				$query = "SELECT MIN(surface_area), MAX(surface_area), AVG(surface_area) FROM lab6.country";
				break;
			case 2:
				$query = "SELECT region, SUM(population) AS total_pop, SUM(surface_area) AS total_area, SUM(gnp) AS total_gnp FROM lab6.country GROUP BY region ORDER BY total_gnp DESC";
				break;
			case 3:
				$query = "SELECT government_form, COUNT(*) AS count, MAX(indep_year) AS most_recent_indep_year FROM lab6.country WHERE indep_year IS NOT NULL GROUP BY government_form ORDER BY count DESC, most_recent_indep_year DESC";
				break;
			case 4:
				$query = "SELECT ctry.name, COUNT(*) AS count FROM lab6.city AS ct INNER JOIN lab6.country AS ctry USING (country_code) GROUP BY ctry.name HAVING COUNT(*)>100 ORDER BY count";
				break;
			case 5:
				$query = "SELECT ctry.name, MAX(ctry.population) AS country_population, SUM(ct.population) AS urban_population, (SUM(ct.population)*100/AVG(ctry.population)) AS urban_pct FROM lab6.country AS ctry INNER JOIN lab6.city AS ct USING(country_code) GROUP BY ctry.name, ctry.population ORDER BY urban_pct";
				break;
			case 6:
				$query = "SELECT ctry.name AS country, ct.name AS largest_city, country_count.population FROM
				(SELECT country_code, MAX(population) AS population FROM lab6.city GROUP BY country_code) AS country_count
				INNER JOIN lab6.city AS ct USING (country_code)
				INNER JOIN lab6.country AS ctry USING (country_code)
				WHERE ct.population = country_count.population
				ORDER BY population DESC";
				break;
			case 7:
				$query = "SELECT ctry.name, COUNT(*) AS count FROM lab6.country AS ctry INNER JOIN lab6.city AS ct USING (country_code) GROUP BY country_code ORDER BY count DESC, ctry.name";
				break;
			case 8:
				$query = "SELECT ctry.name, ct.name AS capital, COUNT(*) AS lang_count FROM lab6.country_language AS cl INNER JOIN lab6.country AS ctry USING (country_code) INNER JOIN lab6.city AS ct ON ctry.capital=ct.id GROUP BY cl.country_code,ctry.name,ct.name HAVING COUNT(*)>7 AND COUNT(*)<13 ORDER BY lang_count DESC, ct.name DESC";
				break;
			case 9:
				$query = "SELECT ctry.name AS country, ct.name AS city, ct.population, (SUM(ct.population) OVER (PARTITION BY ctry.name ORDER BY ct.population DESC)) AS running_total FROM lab6.country AS ctry INNER JOIN lab6.city AS ct USING (country_code) ORDER BY country, running_total";
				break;
			case 10:
				$query = "SELECT ctry.name, cl.language, (rank() OVER (PARTITION BY cl.country_code ORDER BY cl.percentage DESC)) AS popularity_rank FROM lab6.country_language AS cl INNER JOIN lab6.country AS ctry USING (country_code) ORDER BY ctry.name, popularity_rank";
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
