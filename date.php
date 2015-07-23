<?php
date_default_timezone_set ( 'Asia/Kolkata' );

	$tomorrow = mktime ( 0, 0, 0, date ( "m" ), date ( "d" )+1, date ( "Y" ) );
	$today = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) );
	$diff = $tomorrow - $today;
	echo "tomorrow: $tomorrow - today: $today = $diff";
	echo "\n";
	$date = mktime ( 15, 30, 0, date ( "m" ), date ( "d" ), date ( "Y" ) );
	$diff = $date - $today;
	echo "Today 3:30 : $date - today: $today = $diff";
	echo "\n";
	
	$todayDate = date ( 'm/d/Y h:i:s A T', $today );
	echo "Today : $todayDate";
	$time = date ( 'm/d/Y h:i:s A T', $date );
	echo "\n";
	echo "Date : $date";
	echo "\n";
	echo " Time : $time";
	echo "\n";
//http://localhost/~johndondapati/rprajan/php-reports/report/html/?report=mysql/drilldown/customer-orders.sql&macros[range][]=2015-06-23%2000:00:00&macros[range][]=2015-07-22%2023:59:59&macros[id]=3
	?>
