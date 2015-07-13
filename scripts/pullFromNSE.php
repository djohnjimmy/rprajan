<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>
<?php 
require_once('../classes/CsvImporter.php');
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

date_default_timezone_set('Asia/Kolkata');
	
$servername = "localhost";
$username = "root";
$password = "passw0rd";

// Create connection
$mysqli = new mysqli($servername, $username, $password, "stocks");

// Check connection
if($mysqli->connect_errno)
{
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit;
}
//echo "Connected successfully";

if($_POST["datepicker"] != null)
{
	echo "Date picked:" .$_POST['datepicker'];
	$year = date("Y", strtotime($_POST['datepicker']));
	$month = strtoupper(date("M", strtotime($_POST['datepicker'])));
	$day = date("d", strtotime($_POST['datepicker']));;
 
}
else{
	$year = date("Y");
	$month = strtoupper(date("M"));
	$day = date("d");

}


echo "<br /> year = $year";
echo "<br /> month = $month";
echo "<br /> day = $day";


$url = "http://www.nse-india.com/content/historical/EQUITIES/$year/$month/cm$day$month${year}bhav.csv.zip";

echo "<br /> URL = $url";
// Get cURL resource

$curl = curl_init();
$tempFile ="/tmp/cm$day$month${year}bhav.csv.zip";
    $fp = fopen($tempFile, "w");
    curl_setopt ($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FILE, $fp);
	curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

    curl_exec ($curl);
    curl_close ($curl);
	
	$zip = new ZipArchive;
$res = $zip->open($tempFile);
if ($res === TRUE) {
  $zip->extractTo('/tmp/');
  $zip->close();
  echo '<br /> Extraction Completed!';
} else {
  echo 'doh!';
}
	
	
$importer = new CsvImporter("/tmp/cm$day$month${year}bhav.csv",true);

$data = $importer->get(2000);
$newScripCount = 0;
$newStockCount = 0;

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
			
			/*echo "<br/>";
			echo "symbol = $symbol";
			echo "  last = $last";
			echo "  prevclose = $prevclose";
			echo "  TOTTRDQTY = $TOTTRDQTY";
			echo "time = $time";
			echo "timestamp = $timestamp";
			
			echo " gain = $gain";
			echo "<br/>";*/
					
			$selectSql = "SELECT id, SYMBOL FROM stocks.SCRIP WHERE SYMBOL like '$symbol' AND SERIES like '$series'";
			$selectResult = $mysqli->query($selectSql);
			$selectCount = $selectResult->num_rows;
			$selectResult->free();

			if ($selectCount == 0) 
			{
				echo " New SCRIP Entry found. Inserting into the SCRIP TABLE.";
					$insertSql = "INSERT INTO stocks.SCRIP (  symbol, series )
					VALUES ( '$symbol', '$series' )";
					$insertResult = $mysqli->query($insertSql);
			
					//echo "<br /> insert result = $insertResult";
					if ($insertResult) 
					{
						echo "New record created successfully";
						$newScripCount++;
					} 
					else 
					{
						echo "Error: " . $sql . "<br>" . $conn->error;
					}
				}
			else
			{
				//echo "<br/> Existing SCRIP record found";
			}
			
			//Rerun the SQL to check if the insert has made it.
			$selectResult = $mysqli->query( $selectSql);
			$selectCount = $selectResult->num_rows;
			if ($selectCount > 0) 
			{
	
				// output data of each row
				while($hit = $selectResult->fetch_assoc()) 
				{
					$scrip_id = $hit["id"];
					//echo " <br/ >";
					echo "<br>  id: " . $hit["id"]. " - Name: " . $hit["SYMBOL"]. "  TIMESTAMP = " . $timestamp . "  TIME = " . $time;
										
					//$stockSql = "SELECT SCRIP_ID, TIMESTAMP FROM stocks.stocks WHERE SCRIP_ID = $scrip_id AND UPPER(date_format(TIMESTAMP, '%d-%b-%Y')) =  '$time' ";
					$stockSql = "SELECT SCRIP_ID, TIMESTAMP FROM stocks.stocks WHERE SCRIP_ID = $scrip_id AND FROM_UNIXTIME(TIMESTAMP) =  FROM_UNIXTIME($timestamp) ";
					
					$stockResult = $mysqli->query($stockSql);
					
					$stockCount = $stockResult->num_rows;
					
					if($stockCount == 0)
					{
						
						$insertSql = "INSERT INTO stocks.stocks (  last, prevclose, TOTTRDQTY, gain, timestamp, SCRIP_ID)
						VALUES ( $last , $prevclose, $TOTTRDQTY, $gain, FROM_UNIXTIME($timestamp), $scrip_id )";
						
						
						if ($mysqli->query($insertSql)) 
						{
							//echo "New stocks record created successfully";
							$newStockCount++;
							
						} else 
						{
							echo "Error: " . $insertSql . "<br>" . mysqli_error($mysqli);
						}
					}
					else{
						echo "record already exists";
					}
			
				}

			} 
			else 
			{
				echo "COULD NOT find the SCRIP";

			}
			$selectResult->free();

		}
	}

$mysqli->close();

if($newScripCount > 0){
	echo "<br/> ";
	echo " Found $newScripCount SCRIPs. Inserted into the database";

}
else{
	echo "<br/> ";
	echo " No new SCRIPs found ";
}
if($newStockCount > 0){
	echo "<br/> ";
	echo " Found $newStockCount Stocks. Inserted into the database";

}
else{
	echo "<br/> ";
	echo " No new Stocks found ";
}

?>

<body>
</body>
</html>