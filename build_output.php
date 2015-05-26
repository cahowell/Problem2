
<head>
	<title>Spin Results</title>
	<meta http-equiv="content-type"content="application/json;charset=utf-8" />
</head>
<?php
	// Connect and select a database
	$con = mysql_connect("localhost", "root", "");
	mysql_select_db("spin_results");
	
	$filename = "json_response.txt";
	$fh = fopen($filename, 'w') or die("can't open file");
	fclose($fh);
	unlink($filename);

	// Run a query
	$result = mysql_query("SELECT * FROM player");
		
	$outp = "[";
	while ($rs = mysql_fetch_array($result)) {
		if ($outp != "[") {$outp .= ",";}
		$outp .= '{"PlayerID":"'  . $rs["PlayerID"] . '",';
		$outp .= '"Name":"'   . $rs["Name"]        . '",';
		$outp .= '"Credits":"'. $rs["Credits"]     . '",'; 
		$outp .= '"Lifetime_Spins":"'. $rs["Lifetime_Spins"]     . '",'; 
		$outp .= '"Lifetime_Average_Return":"'. $rs["Credits"]/$rs["Lifetime_Spins"]     . '"}'; 
	}
	$outp .="]";

	file_put_contents($filename, $outp . "\n", FILE_APPEND);
	
//$conn->close();
mysql_close();

echo($outp);
?>