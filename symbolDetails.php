<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Pull Data from Yahoo</title>
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
include 'PullSymbolDetailsFromYahoo.php';

/* pChart library inclusions */
include("pchart/class/pData.class.php");
include("pchart/class/pDraw.class.php");
include("pchart/class/pImage.class.php");


function print_array($array){

	echo "<pre>";
	print_r($array);
	echo "</pre>";
}

$symbol = "ASTEC";
$pullParser = new PullSymbolDetailsFromYahoo();

//Pull the file from Yahoo
$stocks = $pullParser->pull($symbol);
print_array($stocks);

?>
<br />
<form action="load.php" method="post" enctype="multipart/form-data">
<br/> Done 
  <input type="submit" value="Back" />
</form>
</body>
</html>
