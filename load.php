<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Load Stocks from NSE</title>
<link rel="stylesheet"
	href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<link rel="stylesheet" href="/resources/demos/style.css">
		<script>
  $(function() {
    $( "#datepicker" ).datepicker();
  });
  </script>
<style>
button {
  position     : absolute;
  left         : 440px;
  top          : 360px;

  padding      : 5px;

  font         : bold .6em sans-serif;
  border       : 2px solid #333;
  border-radius: 5px;
  background   : none;

  cursor       : pointer;

-webkit-transform: rotate(-1.5deg);
   -moz-transform: rotate(-1.5deg);
    -ms-transform: rotate(-1.5deg);
     -o-transform: rotate(-1.5deg);
        transform: rotate(-1.5deg);
}

button:after {
  content: " >>>";
}

button:hover,
button:focus {
  outline   : none;
  background: #000;
  color   : #FFF;
}
label {
  font : 1.2em "typewriter", sans-serif;
}
@font-face{
  font-family : "handwriting";

  src : url('journal.eot');
  src : url('journal.eot?') format('eot'),
        url('journal.woff') format('woff'),
        url('journal.ttf') format('truetype');
}

@font-face{
  font-family : "typewriter";

  src : url('veteran_typewriter.eot');
  src : url('veteran_typewriter.eot?') format('eot'),
        url('veteran_typewriter.woff') format('woff'),
        url('veteran_typewriter.ttf') format('truetype');
}

body {
  font  : 21px sans-serif;

  padding : 2em;
  margin  : 0;

  //background : #222;
}

form {
  position: relative;

  width  : 740px;
  height : 58px;
  margin : 10 auto;

  background: #FFF url(background.png);
}
</style>
</head>

<body >
	<br />

	<p>
		<strong>Disclaimer: </strong>This is a private application meant for
		use by Mr. R. Prabhakar Rao only. Please exit if you are authorized to
		use this application. Thanks!	</p>
	<p>&nbsp;</p>
	<p>If you want to pull from the website, please select date for which
		to pull.</p>
	<form action="pull.php" method="post"
		enctype="multipart/form-data">

		<label for="file">Pull file for date :</label> 
			<input type="text" id="datepicker" name="datepicker"> 
			<input type="submit" value="Pull & Load">
	</form>
	<p>Note: if you do not provide a date, the system will try to pull the
		file for today automatically. If you try to pull the file for a
		previous date which has already been loaded, the data will not be
		loaded again.</p>
<p> <br />
</p>
<p>Do you want to upload a file? </p>
<form action="loadStocks.php" method="post"
		enctype="multipart/form-data">
  <label for="file2">Filename:</label>
  <input type="file" name="stocks"
			id="file2" />
  <input type="submit" value="Upload" />
</form>
<p></p>
</body>
</html>
