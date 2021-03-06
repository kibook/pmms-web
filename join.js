const syncInterval = 1000;
const syncTolerance = 2;

const queueUpdateInterval = 2000;

let media = null;
let currentUrl = null;

function timeToString(time) {
	if (time == null || time == 0) {
		return '--:--:--';
	}

	var h = Math.floor(time / 60 / 60);
	var m = Math.floor(time / 60) % 60;
	var s = Math.floor(time) % 60;

	if (isNaN(h)) h = 0;
	if (isNaN(m)) m = 0;
	if (isNaN(s)) s = 0;

	return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
}

function getCurrentTime(startTime) {
	return ((Date.now() / 1000) - startTime);
}

window.addEventListener('load', () => {
	let url = new URL(window.location);
	let roomKey = url.searchParams.get("room");
	let videoContainer = document.getElementById('video-container');
	let playButton = document.getElementById('play');
	let progressBar = document.getElementById('progress');
	let queueVideoButton = document.getElementById('queue-video');
	let urlField = document.getElementById('url');
	let currentTimecode = document.getElementById('current-timecode');
	let durationTimecode = document.getElementById('duration-timecode');
	let homeButton = document.getElementById('home');
	let nextButton = document.getElementById('next');
	let queueList = document.getElementById('queue-list');
	let loopButton = document.getElementById('loop');
	let fullscreenButton = document.getElementById('fullscreen');
	let volumeSlider = document.getElementById('volume');
	let volumeStatus = document.getElementById('volume-status');
	let lockButton = document.getElementById('lock');
	let catalogButton = document.getElementById('catalog');

	playButton.setPauseIcon = function() {
		this.innerHTML = '<i class="fas fa-pause"></i>';
		this.icon = "pause";
	}

	playButton.setPlayIcon = function() {
		this.innerHTML = '<i class="fas fa-play"></i>';
		this.icon = "play";
	}

	playButton.icon = "pause";

	loopButton.setLoopIcon = function() {
		this.style.color = 'black';
		this.icon = "loop";
	}

	loopButton.setContinueIcon = function() {
		this.style.color = 'grey';
		this.icon = "continue";
	}

	loopButton.icon = "continue";

	lockButton.setLockedIcon = function() {
		this.innerHTML = '<i class="fas fa-lock"></i>';
		this.icon = "locked";
	}

	lockButton.setUnlockedIcon = function() {
		this.innerHTML = '<i class="fas fa-lock-open"></i>';
		this.icon = "unlocked";
	}

	lockButton.icon = "unlocked";

	volumeStatus.updateIcon = function() {
		if (media != null && media.muted) {
			this.innerHTML = '<i class="fas fa-volume-mute"></i>';
		} else if (volumeSlider.value == 0) {
			this.innerHTML = '<i class="fas fa-volume-off"></i>';
		} else if (volumeSlider.value < 50) {
			this.innerHTML = '<i class="fas fa-volume-down"></i>';
		} else {
			this.innerHTML = '<i class="fas fa-volume-up"></i>';
		}
	}

	volumeStatus.updateIcon();

	function disableControls(disabled) {
		lockButton.disabled = disabled;
		playButton.disabled = disabled;
		progressBar.disabled = disabled;
		queueVideoButton.disabled = disabled;
		urlField.disabled = disabled;
		nextButton.disabled = disabled;
		loopButton.disabled = disabled;
		catalogButton.disabled = disabled;
	}

	function enqueueVideo() {
		let url = urlField.value;
		urlField.value = '';
		fetch(`enqueue.php?room=${roomKey}&url=${encodeURI(url)}`);
	}

	setInterval(() => {
		fetch(`sync.php?room=${roomKey}`).then(resp => resp.json()).then(resp => {
			if (resp.url == null) {
				window.location = '.';
			}

			if (resp.locked) {
				if (lockButton.icon == "unlocked") {
					lockButton.setLockedIcon();

					if (resp.is_owner) {
						disableControls(false);
					} else {
						disableControls(true);
					}
				}
			} else {
				if (lockButton.icon == "locked") {
					lockButton.setUnlockedIcon();
					disableControls(false);
				}
			}

			lockButton.disabled = !resp.is_owner;

			if (media == null || currentUrl != resp.url) {
				currentUrl = resp.url;

				if (media != null) {
					media.remove();
				}

				let video = document.createElement('video');
				video.id = 'video';
				video.src = resp.url;
				videoContainer.appendChild(video);

				media = new MediaElement('video');

				media.addEventListener('canplay', () => {
					progressBar.max = media.duration;
					durationTimecode.innerHTML = timeToString(media.duration);

					media.volume = volumeSlider.value / 100;

					media.play();

					media.isReady = true;
				});
			}

			if (media == null) {
				return;
			}

			nextButton.queueId = resp.next;

			if (resp.loop) {
				if (loopButton.icon == "continue") {
					loopButton.setLoopIcon();
				}
			} else {
				if (loopButton.icon == "loop") {
					loopButton.setContinueIcon();
				}
			}

			if (resp.paused == null) {
				progressBar.max = media.duration;
				durationTimecode.innerHTML = timeToString(media.duration);

				let currentTime = (Date.now() / 1000) - resp.start_time;

				if (resp.loop) {
					currentTime %= media.duration;
				} else {
					if (media.isReady && currentTime >= media.duration) {
						if (nextButton.queueId != null) {
							fetch(`dequeue.php?room=${roomKey}&id=${nextButton.queueId}`);
						}

						currentTime = media.duration;

						media.pause();

						return;
					}
				}

				if (Math.abs(media.currentTime - currentTime) > syncTolerance) {
					media.currentTime = currentTime;
				}

				progressBar.value = currentTime;
				currentTimecode.innerHTML = timeToString(currentTime);

				if (media.paused) {
					media.play();
				}

				if (playButton.icon == "play") {
					playButton.setPauseIcon();
				}
			} else {
				if (!media.paused) {
					media.pause();
				}

				if (playButton.icon == "pause") {
					playButton.setPlayIcon();
				}
			}
		});
	}, syncInterval);

	setInterval(() => {
		fetch(`queue.php?room=${roomKey}`).then(resp => resp.json()).then(resp => {
			queueList.innerHTML = '';

			resp.forEach(item => {
				let queueItem = document.createElement('div');
				queueItem.className = "queue-item";
				queueItem.innerHTML = item.title;
				queueItem.addEventListener('click', () => {
					fetch(`dequeue.php?room=${roomKey}&id=${item.id}`);
				});
				queueList.appendChild(queueItem);
			});
		});
	}, queueUpdateInterval);

	playButton.addEventListener('click', function() {
		if (media.paused) {
			fetch(`resume.php?room=${roomKey}`);
		} else {
			fetch(`pause.php?room=${roomKey}`);
		}
	});

	queueVideoButton.addEventListener('click', function() {
		enqueueVideo();
	});

	progressBar.addEventListener('input', function() {
		fetch(`seek.php?room=${roomKey}&time=${this.value}`);
	});

	homeButton.addEventListener('click', function() {
		window.location = '.';
	});

	urlField.addEventListener('keyup', function(e) {
		if (e.code == 'Enter') {
			enqueueVideo();
		}
	});

	nextButton.addEventListener('click', function() {
		if (this.queueId != null) {
			fetch(`dequeue.php?room=${roomKey}&id=${this.queueId}`);
		}
	});

	loopButton.addEventListener('click', function() {
		fetch(`loop.php?room=${roomKey}&loop=${this.icon == "loop" ? "no" : "yes"}&time=${media.currentTime}`);
	});

	fullscreenButton.addEventListener('click', function() {
		if (media != null) {
			media.requestFullscreen();
		}
	});

	volumeSlider.addEventListener('input', function() {
		if (media != null) {
			media.volume = this.value / 100;
			volumeStatus.updateIcon();
		}
	});

	volumeStatus.addEventListener('click', function() {
		if (media != null) {
			media.muted = !media.muted;
			volumeStatus.updateIcon();
		}
	});

	lockButton.addEventListener('click', function() {
		fetch(`lock.php?room=${roomKey}`);
	});

	catalogButton.addEventListener('click', function() {
		window.location = `browse.php?room=${roomKey}`;
	});
});
