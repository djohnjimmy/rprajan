<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Stock Load Results</title>
</head>
<?php  
//Main code
include 'PullFromNSE.php';

$pullParser = new PullFromNSE();

//Check the database connection
echo $pullParser->checkDBConnection ();

//Load the file;
$pullParser->load($_FILES['stocks']['tmp_name']);

?>
<body>
</body>
</html>