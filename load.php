<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Load Stocks from NSE </title>
   <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script>
  $(function() {
    $( "#datepicker" ).datepicker();
  });
  </script>
</head>

<body>
<br/>

<p>
This is a private application meant for use by Mr. R. Prabhakar Rao only. Please exit if you are authorized to use this application. Thanks!
</p>

<br/>

<p> Do you want to upload a file? </p>
<form action="loadStocks.php" method="post" enctype="multipart/form-data">
   <label for="file">Filename:</label> <input type="file" name="stocks" id="file"/>
<input type="submit" value="Submit">
</form>

<p> If you want to pull from the website, please select date for which to pull. </p>
<p> Note: if you do not provide a date, the system will try to pull the file for today automatically. </p>
<p> If you try to pull the file for a previous date which has already been loaded, the data will not be loaded again.</p>
<form action="pullFromNSE.php" method="post" enctype="multipart/form-data">
   
   <label for="file">Pull file for date :</label>
     <input type="text" id="datepicker" name="datepicker">
<input type="submit" value="Submit">
</form>

</body>
</html>