<head><title>Query 13</title></head>
<body>
<?php

	//open a connection to dbase server 
	include 'open.php';

	// echo some basic header info onto the page
	echo "<h2>How many Netflix TVshows were added to Neflix in a month that had over 8,000 daily cases in the given country in 2020?</h2><br>";

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
		//construct an array in which we'll store our data
	    $dataPoints = array();
	
		//Prepare a statement that we can later execute. The ?'s are placeholders for
		//parameters whose values we will set before we run the query.
		if ($stmt = $conn->prepare("CALL Query_13(?)")) {
	
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
	
				//Report result set by visiting each row in it
				while ($row = $result->fetch_row()) {
					array_push($dataPoints, array( "label"=> $row[0], "y"=> $row[1]));
				} 
			
				
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


<html>
<head>
<script>
window.onload = function() {
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
    exportEnabled: true,
	theme: "light2",
	title:{
		text: "Number of Netflix TV Shows Added When Daily Cases Exceeded 8,000 Cases in the given country"
	},
	axisY: {
		title: "Number of Shows"
	},
    axisX: {
		title: "Month in 2020"
	},
	data: [{
		type: "column",
		yValueFormatString: "#,##0.## shows",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
 
}
</script>
</head>
<body>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>                              

