<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Pull Status</title>
<link rel="stylesheet"
	href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="rprajan.css">
	
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<link rel="stylesheet" href="/resources/demos/style.css">
		<script>
  $(function() {
    $( "#datepicker" ).datepicker();
  });
  </script>
<style>

</style>
</head>

<body >
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
<br />
<form action="load.php" method="post" enctype="multipart/form-data">
<br/> Done 
  <input type="submit" value="Back" />
</form>
</body>
</html>
