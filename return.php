<?php
//session_start();
//	if(isset($_SESSION['user']) && isset($_SESSION['id'])){
//		header("location:index.php");
//	}

	require_once('./inc/PdoConnect.php');
	require_once('./inc/config.inc.php');
  require_once('./Jwt.php');
	$dbh = PdoConnect::getInstance($dsn,$user,$pswd);
  
  $verify1 = Jwt::verifyToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJqd3RfYWRtaW4iLCJpYXQiOjE1MzgwMTQyMzQsImV4cCI6MTUzODAyMTQzNCwibmJmIjoxNTM4MDE0Mjk0LCJzdWIiOiJ3d3cuYWRtaW4uY29tIiwianRpIjoiMGE1YzVjZTNhOGNhOGY0YTVjNGY0NzA5OGMxNWZhYTEiLCJjY2MiOiJhc2Rhc2Rhc2QifQ.uatL97-x8nAWPVGHUCUxhcVUwlhTVkr1y5tn-6WbBRU');
  print_r($verify1);
?>