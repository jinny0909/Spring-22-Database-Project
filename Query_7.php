<head><title>Query 7</title></head>
<body>
<?php

	//open a connection to dbase server 
	include 'open.php';

	// echo some basic header info onto the page
	echo "<h2>In which month(s) of 2021 did the given country record the highest number of deaths per million people?</h2><br>";

	//Override the PHP configuration file to display all errors
	//This is useful during development but generally disabled before release
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', true);

	//Collect the posted value in a variable called $item
	$country = $_POST['country'];

	//Determine if any input was actually collected
	if (empty($country)) {
		echo "empty <br><br>";
	
	} else {
	
		echo $country."<br><br>";
	
		//Prepare a statement that we can later execute. The ?'s are placeholders for
		//parameters whose values we will set before we run the query.
		if ($stmt = $conn->prepare("CALL Query_7(?)")) {
	
		//Attach the ? in prepared statements to variables (even if those variables
		//don't hold the values we want yet).  First parameter is a list of types of
		//the variables that follow: 's' means string, 'i' means integer, 'd' means
		//double. E.g., for a statment with 3 ?'s, where middle parameter is an integer
		//and the other two are strings, the first argument included should be "sis".
		$stmt->bind_param("s", $country);
	
		//Run the actual query
		if ($stmt->execute()) {
	
			//Store result set generated by the prepared statement
			$result = $stmt->get_result();
	
			if (($result) && ($result->num_rows != 0)) {
		
				//Create table to display results
				echo "<table border=\"1px solid black\">";
				echo "<tr><th> The Month of 2021 </th></tr>";
	
				//Report result set by visiting each row in it
				while ($row = $result->fetch_row()) {
					echo "<tr>";
					echo "<td>".$row[0]."</td>";
					echo "</tr>";
				} 
			
		
				echo "</table>";
				
			}	else {
			//if ($result->num_rows == 0) {
				//Result contains no rows at all
				echo "No record found for the specified country";
	
			}
	
			//We are done with the result set returned above, so free it
			$result->free_result();
		
		} else {
	
			//Call to execute failed, e.g. because server is no longer reachable,
		//or because supplied values are of the wrong type
			echo "Execute failed.<br>";
		}
	
		//Close down the prepared statement
		$stmt->close();
	
		} else {
	
			//A problem occurred when preparing the statement; check for syntax errors
			//and misspelled attribute names in the statement string.
		echo "Prepare failed.<br>";
		$error = $conn->errno . ' ' . $conn->error;
		echo $error; 
		}
	
	}
	
	//Close the connection created in open.php
	$conn->close();
?>
</body>