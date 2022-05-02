<?php
session_start();
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title>pmms</title>
		<script src="https://kit.fontawesome.com/5537a772c3.js" crossorigin="anonymous"></script>
		<script async defer src="https://buttons.github.io/buttons.js"></script>
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
				<input type="text" name="url" id="url" placeholder="Enter media URL...">
				<button type="submit">&#xf35a;</button>
			</form>
			<div id="github-buttons">
				<a class="github-button" href="https://github.com/kibook/pmms-web/subscription" data-icon="octicon-eye" data-size="large" data-show-count="true" aria-label="Watch kibook/pmms-web on GitHub">Watch</a>
				<a class="github-button" href="https://github.com/kibook/pmms-web" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star kibook/pmms-web on GitHub">Star</a>
				<a class="github-button" href="https://github.com/kibook/pmms-web/fork" data-icon="octicon-repo-forked" data-size="large" data-show-count="true" aria-label="Fork kibook/pmms-web on GitHub">Fork</a>
				<a class="github-button" href="https://github.com/kibook/pmms-web/issues" data-icon="octicon-issue-opened" data-size="large" data-show-count="true" aria-label="Issue kibook/pmms-web on GitHub">Issue</a>
				<a class="github-button" href="https://github.com/kibook/pmms-web/discussions" data-icon="octicon-comment-discussion" data-size="large" aria-label="Discuss kibook/pmms-web on GitHub">Discuss</a>
			</div>
		</div>
	</body>
</html>
