<?php

//Main code
include 'PullFromNSE.php';

if ($_POST ["datepicker"] != null) {
	
	echo "Date picked:" . $_POST ['datepicker'];
	$year = date ( "Y", strtotime ( $_POST ['datepicker'] ) );
	$month = strtoupper ( date ( "M", strtotime ( $_POST ['datepicker'] ) ) );
	$day = date ( "d", strtotime ( $_POST ['datepicker'] ) );
} else {
	$year = date ( "Y" );
	$month = strtoupper ( date ( "M" ) );
	$day = date ( "d" );
}

$pullParser = new PullFromNSE();

//Check the database connection
echo $pullParser->checkDBConnection ();

//Pull the file from NSE
$pullParser->pull($day, $month, $year);

//Load the file;
$pullParser->load("/tmp/cm$day$month${year}bhav.csv");

?>
