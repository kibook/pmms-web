<?php
session_start();
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title>pmms</title>
		<script src="https://kit.fontawesome.com/5537a772c3.js" crossorigin="anonymous"></script>
		<script src="index.js"></script>
		<link rel="stylesheet" href="index.css">
	</head>
	<body>
		<div id="main">
			<div id="title">
				<div>🐩</div>
				<div>PMMS</div>
			</div>
			<form action="create.php" id="create-room">
				<input type="hidden" name="lock" id="lock">
				<button type="button" id="lock-btn">
					<i class="fas fa-lock-open"></i>
				</button>
				<input type="text" name="url" placeholder="Enter media URL...">
				<button type="submit">&#xf35a;</button>
			</form>
		</div>
	</body>
</html>
