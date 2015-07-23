<?php
include 'classes/CsvImporter.php';
ini_set ( 'display_startup_errors', 1 );
ini_set ( 'display_errors', 1 );
error_reporting ( - 1 );
date_default_timezone_set ( 'Asia/Kolkata' );


class PullFromNSE {
	// DB Server settings
// 	private $servername = "stocks.c9l4wumxzfjg.us-east-1.rds.amazonaws.com";
	private $servername = "localhost";
	private $username = "root";
	private $password = "passw0rd";
	public $mysqli;
	
	function __construct() {
		if (!function_exists('mysqli_init') && !extension_loaded('mysqli')) {
			echo 'no mysqli :(';
			exit();
		} 
		
		$this->mysqli = new mysqli($this->servername, $this->username, $this->password, "stocks" );
	}
	
	// Create connection
	
	public function checkDBConnection() {
		// Check connection
		
		if ($this->mysqli->connect_errno) {
			printf ( "Connect failed: %s\n", $this->mysqli->connect_error );
			exit ();
		} else {
// 			echo "Connection successful";
		}
	}
	
	public function pull($day, $month, $year) {
		
		
		echo "<br /> year = $year";
		echo "<br /> month = $month";
		echo "<br /> day = $day";
		
		$url = "http://www.nse-india.com/content/historical/EQUITIES/$year/$month/cm$day$month${year}bhav.csv.zip";
		
		echo "<br /> URL = $url";
		// Get cURL resource
		
		if (!$this->url_exists($url)) {
			echo "<br /> The file cm$day$month${year}bhav.csv.zip does not exist on the server yet!  ";
			exit();
		}
		
		$curl = curl_init ();
		$tempFile = "/tmp/cm$day$month${year}bhav.csv.zip";
		$fp = fopen ( $tempFile, "w" );
		curl_setopt ( $curl, CURLOPT_URL, $url );
		curl_setopt ( $curl, CURLOPT_FILE, $fp );
		curl_setopt ( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13' );
		
		curl_exec ( $curl );
		curl_close ( $curl );
		
			
			
		$zip = new ZipArchive ();
		$res = $zip->open ( $tempFile );
		if ($res === TRUE) {
			$zip->extractTo ( '/tmp/' );
			$zip->close ();
			echo '<br /> Extraction Completed!';
		} else {
			echo '<br /> EXTRACTION FAILED';
		}
	}
	
	public function url_exists($url) 
	{
	    if (!$fp = curl_init($url)) 
	    	return false;
	    else
		    return true;
	}
	public function load($file){

		if (!file_exists($file)) 
		{	
			echo "<br /> The file $file does not exist";
			exit();
		}

		$importer = new CsvImporter ( $file, true );
		
		$data = $importer->get ( 2000 );
		$newScripCount = 0;
		$newStockCount = 0;
		
		foreach ( $data as $line ) {
			foreach ( $line as $row ) {
				$record = explode ( ",", $row );
				$symbol = $record [0];
				$series = $record [1];
				$last = $record [6];
				$prevclose = $record [7];
				$TOTTRDQTY = $record [8];
				$time = $record [10];
				$timestamp = strtotime ( $time );
				$gain = round(floatval($last) - floatval($prevclose), 2);
		
// 				 echo "<br/>"; 
// 				 echo " symbol = $symbol"; 
// 				 echo " last = $last";
// 				 echo " prevclose = $prevclose"; 
// 				 echo " TOTTRDQTY = $TOTTRDQTY"; 
// 				 echo " time = $time"; 
// 				 echo " timestamp = $timestamp"; 
// 				 echo " gain = $gain"; 
// 				 echo "<br/>";
				
		
				$selectSql = "SELECT id, SYMBOL FROM stocks.SCRIP WHERE SYMBOL like '$symbol' AND SERIES like '$series'";
				$selectResult = $this->mysqli->query ( $selectSql );
				$selectCount = $selectResult->num_rows;
				$selectResult->free ();
		
				if ($selectCount == 0) {
					// 					echo " New SCRIP Entry found. Inserting into the SCRIP TABLE.";
					$insertSql = "INSERT INTO stocks.SCRIP (  symbol, series, CREATED_TIME)
					VALUES ( '$symbol', '$series', NOW() )";
					$insertResult = $this->mysqli->query ( $insertSql );
						
					// echo "<br /> insert result = $insertResult";
					if ($insertResult) {
						// 						echo "New record created successfully";
						$newScripCount ++;
					} else {
						echo "Error: " . $sql . "<br>" . $conn->error;
					}
				} else {
					// echo "<br/> Existing SCRIP record found";
				}
		
				// Rerun the SQL to check if the insert has made it.
				$selectResult = $this->mysqli->query ( $selectSql );
				$selectCount = $selectResult->num_rows;
				if ($selectCount > 0) {
						
					// output data of each row
					while ( $hit = $selectResult->fetch_assoc() ) {
						// 						var_dump($hit);
						$scrip_id = intval($hit ["id"]);
						// echo " <br/ >";
						// 						echo "<br>  id: " . $scrip_id . " - Name: " . $hit ["SYMBOL"] . "  TIMESTAMP = " . $timestamp . "  TIME = " . $time;
		
						$stockSql = "SELECT SCRIP_ID, TIMESTAMP FROM stocks.stocks WHERE SCRIP_ID = $scrip_id AND UPPER(date_format(TIMESTAMP, '%d-%b-%Y')) like '$time'";
						// 						$stockSql = "SELECT SCRIP_ID, TIMESTAMP FROM stocks.stocks WHERE SCRIP_ID = $scrip_id AND FROM_UNIXTIME(TIMESTAMP) =  FROM_UNIXTIME($timestamp) ";
						$stockResult = $this->mysqli->query ( $stockSql );
		
						$stockCount = $stockResult->num_rows;
		
						if ($stockCount == 0) {
							
						$insertSql = "INSERT INTO stocks.stocks (  last, prevclose, TOTTRDQTY, gain, timestamp, SCRIP_ID, CREATED_TIME)
						VALUES ( '$last' , '$prevclose', $TOTTRDQTY, '$gain', FROM_UNIXTIME($timestamp), $scrip_id, NOW())";
									
								if ($this->mysqli->query ( $insertSql )) {
								// echo "New stocks record created
							// successfully";
							$newStockCount ++;
								
						} else {
						echo "Error: " . $insertSql . "<br>" . mysqli_error ( $this->mysqli );
						}
						} else {
						// 							echo "record already exists";
					}
						
					}
		
					} else {
					echo "<br />COULD NOT find the SCRIP in database with $symbol and $series";
		
				}
				$selectResult->free ();
					
				}
				}
		
				$this->mysqli->close ();
		
				if ($newScripCount > 0) {
				echo "<br/> ";
				echo " Found $newScripCount SCRIPs. Inserted into the database";
		
				} else {
					echo "<br/> ";
					echo " No new SCRIPs found ";
				}
				if ($newStockCount > 0) {
					echo "<br/> ";
					echo " Found $newStockCount Stocks. Inserted into the database";
		
				} else {
				echo "<br/> ";
				echo " No new Stocks found ";
				}
	}
}

?>