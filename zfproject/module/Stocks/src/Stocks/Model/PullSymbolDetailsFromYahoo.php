<?php
namespace Stocks\Model;
use Stocks\Model\RealTimeData;

ini_set ( 'display_startup_errors', 1 );
ini_set ( 'display_errors', 1 );
// error_reporting ( - 1 );
error_reporting(E_ALL);

date_default_timezone_set ( 'Asia/Kolkata' );
// date_default_timezone_set ( 'America/New_York' );

class PullSymbolDetailsFromYahoo {

	private $url_prefix;
	private $days=1;
	private $url_suffix;
	
	function __construct() {
		$this->url_prefix ="http://chartapi.finance.yahoo.com/instrument/1.0/";
		$this->url_suffix =".NS/chartdata;type=quote;range=". $this->days. "d/csv/";
	}

	function print_array($array){

		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
	function pull($symbol){
		$url= $this->url_prefix.$symbol.$this->url_suffix;
		$CsvString = file_get_contents($url);
		
// 		$curl = curl_init ();
// 		$tempFile = "/tmp/".$symbol.".csv";
// 		$fp = fopen ( $tempFile, "w" );
// 		curl_setopt ( $curl, CURLOPT_URL, $url );
// 		curl_setopt ( $curl, CURLOPT_FILE, $fp );
// 		curl_setopt ( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13' );
		
// 		curl_exec ( $curl );
// 		curl_close ( $curl );
		
		
// 		echo "<pre>";
// 		print_r($CsvString);
// 		echo "</pre>";

		$count =0;
		$rowCount = 0;
		$stocks = array();
		$total = 0;
		
		$Data = str_getcsv($CsvString, "\n"); //parse the rows
		foreach($Data as &$Row) {
			$Row = str_getcsv($Row, ";"); //parse the items in rows
			if($count >16){
// 				values:Timestamp,close,high,low,open,volume

				$rowCount++;
				$Record = explode(",", $Row[0]);
				$realTimeData = new RealTimeData();
				$realTimeData->id = $rowCount;
				$realTimeData->timestamp = $Record [0];
				$realTimeData->close = $Record [1];
				$realTimeData->high = $Record [2];
				$realTimeData->low = $Record [3];
				$realTimeData->open = $Record [4];
				$realTimeData->symbol = $symbol;
// 				$realTimeData->gain = round(floatval($realTimeData->last)) - round(floatval($realTimeData->close));
				$realTimeData->volume = $Record [5];
				$realTimeData->dateTime = date('h:i:s A', $realTimeData->timestamp);

				$total = $total + $realTimeData->volume;
				$realTimeData->total = $total;
				
// 				$gain = round(floatval($last) - floatval($close), 2);
// 				$today = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
				
				$stocks[$realTimeData->timestamp] = $realTimeData;
				
// 				echo "<br /> Row $rowCount :";
// 				echo " Timestamp: $time , Close: $close, High: $high, Low : $low, Open : $open, Volume : $volume";
				
				}
			$count++;
		}
// 		foreach ($stocks as $key => $val) {
// 			echo "$key = $val  : ".date('m/d/Y h:i:s A T', $key) ."<br/> ";
// 		}

	
//         if (!empty($stocks)) {
//             krsort($stocks, SORT_NUMERIC);
//         }		
		
// 		$this->print_array($stocks);
		return $stocks;
	}
	
	function __destruct() {
		
		// TODO - Insert your code here
	}
}

?>