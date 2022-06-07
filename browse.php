<?php
include "pmms.php";

session_start();
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title>pmms</title>
		<script src="https://kit.fontawesome.com/5537a772c3.js" crossorigin="anonymous"></script>
		<script async defer src="https://buttons.github.io/buttons.js"></script>
		<script src="browse.js"></script>
		<link rel="stylesheet" href="browse.css">
	</head>
	<body>
		<button id="home">
			<i class="fas fa-home"></i>
		</button>
		<button id="back">
			<i class="fas fa-grip-horizontal"></i>
		</button>
		<div id="catalog"></div>
	</body>
</html>
