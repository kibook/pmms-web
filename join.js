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
	let sourceLink = document.getElementById('source');
	let mediaTitle = document.getElementById('title');
	let nextButton = document.getElementById('next');
	let queueList = document.getElementById('queue-list');
	let loopButton = document.getElementById('loop');
	let fullscreenButton = document.getElementById('fullscreen');
	let volumeSlider = document.getElementById('volume');
	let volumeStatus = document.getElementById('volume-status');

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
		this.innerHTML = '<i class="fas fa-retweet"></i>';
		this.icon = "loop";
	}

	loopButton.setContinueIcon = function() {
		this.innerHTML = '<i class="fas fa-arrow-right"></i>';
		this.icon = "continue";
	}

	loopButton.icon = "continue";

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

	setInterval(() => {
		fetch(`sync.php?room=${roomKey}`).then(resp => resp.json()).then(resp => {
			if (resp.url == null) {
				window.location = '.';
			}

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

					if (media.youTubeApi) {
						let title = media.youTubeApi.getVideoData().title;

						sourceLink.innerHTML = title;
						sourceLink.href = media.src;

						document.title = 'pmms - Watching ' + title;
					} else {
						sourceLink.innerHTML = media.src;
						sourceLink.href = media.src;

						document.title = 'pmms - Watching ' + media.src;
					}

					media.volume = volumeSlider.value / 100;

					media.play();

					media.isReady = true;
				});
			}

			if (media == null) {
				return;
			}

			nextButton.queueId = resp.next;

			if (resp.paused == null) {
				progressBar.max = media.duration;
				durationTimecode.innerHTML = timeToString(media.duration);

				let currentTime = (Date.now() / 1000) - resp.start_time;

				if (resp.loop) {
					currentTime %= media.duration;

					if (loopButton.icon == "continue") {
						loopButton.setLoopIcon();
					}
				} else {
					if (loopButton.icon == "loop") {
						loopButton.setContinueIcon();
					}

					if (media.isReady && currentTime >= media.duration) {
						if (nextButton.queueId != null) {
							fetch(`dequeue.php?room=${roomKey}&id=${nextButton.queueId}`);
						}

						currentTime = media.duration;
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
				queueItem.innerHTML = item.url;
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
		let url = urlField.value;
		urlField.value = '';
		fetch(`enqueue.php?room=${roomKey}&url=${encodeURI(url)}`);
	});

	progressBar.addEventListener('input', function() {
		fetch(`seek.php?room=${roomKey}&time=${this.value}`);
	});

	homeButton.addEventListener('click', function() {
		window.location = '.';
	});

	urlField.addEventListener('keyup', function(e) {
		if (e.code == 'Enter') {
			let url = this.value;
			this.value = '';
			fetch(`enqueue.php?room=${roomKey}&url=${encodeURI(url)}`);
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
});
