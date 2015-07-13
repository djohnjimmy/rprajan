<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Stock Load Results</title>
</head>
<?php  
require_once('CsvImporter.php');
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
//error_reporting(-1);
// set the default timezone to use. Available since PHP 5.1
date_default_timezone_set('Asia/Kolkata');
	
$servername = "localhost";
$username = "root";
$password = "passw0rd";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";


$importer = new CsvImporter($_FILES['stocks']['tmp_name'],true);

$data = $importer->get(2000);

	foreach($data as $line)
	{
		foreach($line as $row)
		{
			$record = explode(",", $row);
			$symbol = $record[0];
			$series = $record[1];
			$last = $record[6];
			$prevclose = $record[7];
			$TOTTRDQTY = $record[8];
			$time = $record[10];
			$timestamp = strtotime($time);
			$gain = $last - $prevclose;
			
			echo "<br/>";
			echo "symbol = $symbol";
			echo "  last = $last";
			echo "  prevclose = $prevclose";
			echo "  TOTTRDQTY = $TOTTRDQTY";
			echo "time = $time";
			echo "timestamp = $timestamp";
			
			echo " gain = $gain";
			echo "<br/>";
			
			$sql = "SELECT id, SYMBOL FROM stocks.SCRIP WHERE SYMBOL like '$symbol' AND SERIES like '$series'";
			$result = $conn->query($sql);
			
			if ($result->num_rows == 0) 
			{
				echo " New SCRIP Entry found. Inserting into the SCRIP TABLE.";
					$insertSql = "INSERT INTO stocks.SCRIP (  symbol, series )
					VALUES ( '$symbol', '$series' )";
					
					if ($conn->query($insertSql) === TRUE) 
					{
						echo "New record created successfully";
					} 
					else 
					{
						echo "Error: " . $sql . "<br>" . $conn->error;
					}
			}
			
			$sql = "SELECT id, SYMBOL FROM stocks.SCRIP WHERE SYMBOL like '$symbol' AND SERIES like '$series'";
			$result = $conn->query($sql);
		
			if ($result->num_rows > 0) 
			{
				// output data of each row
				while($hit = $result->fetch_assoc()) 
				{
					$scrip_id = $hit["id"];
					echo "id: " . $hit["id"]. " - Name: " . $hit["SYMBOL"]. "<br>";
					$insertSql = "INSERT INTO stocks.stocks (  last, prevclose, TOTTRDQTY, gain, timestamp, SCRIP_ID)
					VALUES ( $last , $prevclose, $TOTTRDQTY, $gain, FROM_UNIXTIME($timestamp), $scrip_id )";
					
					if ($conn->query($insertSql) === TRUE) 
					{
						echo "New record created successfully";
					} 
					else 
					{
						echo "Error: " . $sql . "<br>" . $conn->error;
					}
				}
			} 
			else 
			{
				echo "COULD NOT find the SCRIP";

			}
			echo "<br/>";
			echo "<br/>";
	
		}
	}
	$conn->close();

?>
<body>
</body>
</html>