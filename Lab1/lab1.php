<html>
<head/>
<body>
<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
  <table border="1">
     <tr><td>Number of Rows:</td><td><input type="text" name="rows" /></td></tr>
     <tr><td>Number of Columns:</td><td><select name="columns">
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="4">4</option>
    <option value="8">8</option>
    <option value="16">16</option>

  </select>
</td></tr>
   <tr><td>Operation:</td><td><input type="radio" name="operation" value="multiplication" checked="yes">Multiplication</input><br/>
  <input type="radio" name="operation" value="addition">Addition</input>
  </td></tr>
  </tr><td colspan="2" align="center"><input type="submit" name="submit" value="Generate" /></td></tr>
</table>
</form>

<?php

	/*
		This function does the math for the addition table
		It adds the counter for the row and the counter for the column together and prints out the sum
	*/
	function addition(){
		echo "<p>The " . $_POST["rows"] . " x " . $_POST["columns"] . " addition table.</p>\n";
		echo "<table border = 1>\n";
		//Goes through each row
		for($row = 0; $row <= $_POST["rows"]; $row++){
			echo "\t<tr>\n";
			//Goes through each column of each row
			for($column = 0; $column <= $_POST["columns"]; $column++){
				//If the table box is not a top or left hand border, adds the numbers together unbolded
				if($row != 0 && $column != 0)
					echo "\t\t<td align='center'>" . ($row + $column) . "</td>\n";
				//If it is a column header, prints the column number in bold
				elseif($row == 0)
					echo "\t\t<td align='center'><strong>" . $column . "</strong></td>\n";
				//If it is a row header, prints the row number in bold
				elseif($column == 0)
					echo "\t\t<td align='center'><strong>" . $row . "</strong></td>\n";
			}
			echo "\t</tr>\n";
		}
		echo "</table>\n";
	}

	/*
		This function does the math for the multiplication table
		It multiplies the counter for the row and the counter for the column together and prints out the product
	*/
	function multiply(){
		echo "<p>The " . $_POST["rows"] . " x " . $_POST["columns"] . " multiplication table.</p>\n";
		echo "<table border = 1>\n";
		//Goes through each row
		for($row = 0; $row <= $_POST["rows"]; $row++){
			echo "\t<tr>\n";
			//Goes through each column of each row
			for($column = 0; $column <= $_POST["columns"]; $column++){
				//If the table box is neither the top or left hand border, multiplies the numbers together unbolded
				if($row != 0 && $column != 0)
					echo "\t\t<td align='center'>" . ($row * $column) . "</td>\n";
				//If it is a column header, prints the column number in bold
				elseif($row == 0)
					echo "\t\t<td align='center'><strong>" . $column . "</strong></td>\n";
				//If it is a row header, prints the row number in bold
				elseif($column == 0)
					echo "\t\t<td align='center'><strong>" . $row . "<strong></td>\n";
			}
			echo "\t</tr>\n";
		}
		echo "</table>\n";
	}

	//Checks if the submit button was pressed
	if(isset($_POST['submit'])){
		//Checks if the rows entered is numeric and positive
		if(is_numeric($_POST["rows"]) && $_POST["rows"] > 0){
			if($_POST["operation"] == "addition"){
				addition();
			}
			else{
				multiply();
			}
		}
		else{
			echo "<p>Invalid rows and/or columns parameters.</p>\n";
		}
	}

?>
</body>
</html>
