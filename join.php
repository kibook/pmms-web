<?php
session_start();
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>pmms</title>
		<script src="https://kit.fontawesome.com/5537a772c3.js" crossorigin="anonymous"></script>
		<script src="mediaelement/mediaelement.min.js"></script>
		<script src="mediaelement/dailymotion.min.js"></script>
		<script src="mediaelement/facebook.min.js"></script>
		<script src="mediaelement/soundcloud.min.js"></script>
		<script src="mediaelement/twitch.min.js"></script>
		<script src="mediaelement/vimeo.min.js"></script>
		<script src="join.js"></script>
		<link rel="stylesheet" href="join.css">
	</head>
	<body>
		<div id="video-container"></div>
		<div id="controls-container" class="hover-menu-container">
			<div id="controls" class="hover-menu">
				<button id="play">
					<i class="fas fa-pause"></i>
				</button>
				<input type="range" id="progress" min="0" max="0" value="0">
				<div id="timecodes">
					<span id="current-timecode">00:00:00</span>/<span id="duration-timecode">--:--:--</span>
				</div>
				<div id="volume-control">
					<div id="volume-status">
						<i class="fas fa-volume-up"></i>
					</div>
					<input type="range" id="volume" min="0" max="100" value="100">
				</div>
				<button id="fullscreen">
					<i class="fas fa-expand"></i>
				</button>
			</div>
		</div>
		<div id="queue-container" class="hover-menu-container">
			<div id="queue" class="hover-menu">
				<div id="queue-title"><i class="fas fa-list"></i> Queue</div>
				<div id="add-media">
					<button id="catalog"><i class="fas fa-grip-horizontal"></i></button><input id="url" placeholder="Enter media URL..."><button id="queue-video"><i class="fas fa-plus"></i></button>
				</div>
				<div id="queue-list"></div>
				<div id="queue-controls">
					<button id="loop" style="color: grey;">
						<i class="fas fa-retweet"></i>
					</button>
					<button id="next">
						<i class="fas fa-step-forward"></i>
					</button>
				</div>
			</div>
		</div>
		<div id="room-settings-container" class="hover-menu-container">
			<div id="room-settings" class="hover-menu">
				<div id="room-settings-title"><i class="fas fa-cog"></i> Room Settings</div>
				<div id="room-settings-main">
					<button id="lock">
						<i class="fas fa-lock-open"></i>
					</button>
				</div>
				<button id="home">
					<i class="fas fa-home"></i>
				</button>
			</div>
		</div>
	</body>
</html>
