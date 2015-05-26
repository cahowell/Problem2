<?php

	//mysql_query("START TRANSACTION");
	
	// Connect and select a database
	$con = mysql_connect("localhost", "root", "");
	if (!$con) {
		die('Could not connect: ' . mysql_error());
	} else {
		echo "connected" . "<br />\n";
	}
	mysql_select_db("spin_results");
	

	// Run a query
	$beforeResult = mysql_query("SELECT * FROM player");
	
?>


<head>
	<title>Spin Results</title>
	<meta http-equiv="content-type"content="text/html;charset=utf-8" />
</head>

<body>
	
	<table border="1" cellpadding="2" cellspacing="3"
		summary="Table holds player information">
			<caption>Before Image</caption>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Credits</th>
				<th>Lifetime Spins</th>
				<th>Salt Value</th>
			</tr>	
		
	<?php
	
	// Before Image
	// Loop through all table rows
	while ($xrow = mysql_fetch_array($beforeResult)) {
		echo "<tr>";
		echo "<td>" . $xrow['PlayerID'] . "</td>";
		echo "<td>" . $xrow['Name'] . "</td>";
		echo "<td>" . $xrow['Credits'] . "</td>";
		echo "<td>" . $xrow['Lifetime_Spins'] . "</td>";
		echo "<td>" . $xrow['Salt_Value'] . "</td>";
		echo "</tr>";
	}
	mysql_free_result($beforeResult);
	
	$arr_length = 0;
	$outData = array();
	$playerID = $hash = "";
	$name = $saltValue = "";
	$numSpins = $coinsWon = $coinsBet = $error = 0;
	$filename = $con = "";
	$tmpData = "";
	$fp = "";
	$sql = "";
	
	$aryPlayerID = 0;
	$aryName = 1;
	$aryCoinsWon = 2;
	$aryCoinsBet = 3;
	$aryNumSpins = 4;
	$aryHash = 5;

		// read in file with result info into array 1 line at a time
		$row = 1;
		$filename = "problem2_workfile.txt";
		$fp = fopen($filename, 'r'); 
		while (($outData = fgetcsv($fp, filesize($filename)))!== FALSE) {
			//echo "Reading file..";
			$arr_length = count($outData);
			$playerID = $outData[$aryPlayerID];
			$name = $outData[$aryName];
			$coinsWon = $outData[$aryCoinsWon];
			$coinsBet = $outData[$aryCoinsBet];
			$numSpins = $outData[$aryNumSpins];
			$hash = $outData[$aryHash];
			
			//Validate data from file
			if (empty($playerID)) {
				echo "Name is required on line " . $row . "<br />\n";
				$error = true;
			} elseif (!preg_match("/^[0-9 ]+$/",$playerID)) {
				echo "Player ID is invalid on line " . $row . "<br />\n";
				$error = true;
			} else {
				$error = false;
			}

			if (empty($hash)) {
				echo "Password is required on line " . $row . "<br />\n";
				$error = true;
			} else {
				$saltValue = crypt($hash, '1001');
			}
			
			if (empty($coinsWon)) {
				$coinsWon == 0;
			}
			if (!is_numeric($coinsWon)) {
				echo "Coins Won is invalid on line " . $row . "<br />\n";
				$error = true;
			}
			if (empty($coinsBet)) {
				$coinsBet == 0;
			}
			if (!is_numeric($coinsBet)) {
				echo "Coins Bet is invalid on line " . $row . "<br />\n";
				$error = true;
			}		
			//echo "Error = " . (int)$error . " for line " . $row . "  <br />\n";
			if (!$error) {
				echo "Player ID " . $playerID . " won " . $coinsWon . " coins using " . $numSpins . " spins <br />\n";
			} else {
				echo "Player ID " . $playerID . " not updated <br />\n";
			}
			
			if (!$error) {
				$sql = "SELECT * FROM player WHERE PlayerID = $playerID";
				$result = mysql_query($sql);
				
				if ($result) {
					echo "Select Successful.  Line " . $row . " updated for Player ID " . $playerID . "<br />\n";
				} else {
					echo " SQL Select Error on line " . $row . " error: " . mysql_error() . "<br />\n";
				}
				
				
				if ($result) {
					// Update table
					$sql = "UPDATE PLAYER SET Credits = Credits + $coinsWon, LifeTime_Spins = LifeTime_Spins + $numSpins WHERE PlayerID = $playerID";
					$result = mysql_query($sql);
					if ($result) {
						echo "Line " . $row . " updated for Player ID " . $playerID . "<br />\n";
					} else {
						echo " SQL Update Error on line " . $row . " error: " . mysql_error() . "<br />\n";
					}
					
				} else {
					// Insert into table
					$sql = "INSERT INTO player(`PlayerID`, `Name`, `Credits`, `Lifetime_Spins`, `Salt_Value`)". 
						"VALUES ('$playerID', '$name', '$coinsWon', '$numSpins', '$saltValue')";
					$result = mysql_query($sql);
					if ($result) {
						echo "Line " . $row . " inserted for Player ID " . $playerID . "<br />\n";
					} else {
						echo " SQL Insert Error on line " . $row . " error: " . mysql_error() . "<br />\n";
					}
				}	
			}	$row++;
		}
echo "<br />\n";
?>	
	<table border="1" cellpadding="2" cellspacing="3"
		summary="Table holds player information">
			<caption>After Image</caption>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Credits</th>
				<th>Lifetime Spins</th>
				<th>Salt Value</th>
			</tr>
	<?php		
	$afterResult = mysql_query("SELECT * FROM player");

		// After Image
		// Loop through all table rows
		while ($xrow = mysql_fetch_array($afterResult)) {
			echo "<tr>";
			echo "<td>" . $xrow['PlayerID'] . "</td>";
			echo "<td>" . $xrow['Name'] . "</td>";
			echo "<td>" . $xrow['Credits'] . "</td>";
			echo "<td>" . $xrow['Lifetime_Spins'] . "</td>";
			echo "<td>" . $xrow['Salt_Value'] . "</td>";
			echo "</tr>";
		}

		mysql_free_result($afterResult);
		//mysql_query("ROLLBACK");
		
		mysql_close();
	?>
	</table>
</body>
</html>