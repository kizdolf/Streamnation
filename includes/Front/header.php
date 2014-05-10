<?php
  include __DIR__.'/../main_controller.php';
?>

<!DOCTYPE html>
<html class="no-js">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>LuckyBox</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,400,600' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/main.css">
		<script src="./js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
		<script type="text/javascript">

		  function begin(url)
		  {
			document.Player.playFile(url, 0);
			document.Player.play();
		  }

		  function play_gif()
		  {
		  	
		  }
		</script>

	</head>
	<body onload="play_gif();">
	<div class="overlay hidden">
	</div>