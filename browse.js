function addCatalogEntryClickListener(div, entry, roomKey) {
	div.addEventListener('click', () => {
		if (entry.url == null) {
			if (roomKey == null) {
				window.location = `browse.php?series=${entry.id}`;
			} else {
				window.location = `browse.php?room=${roomKey}&series=${entry.id}`;
			}
		} else {
			if (roomKey == null) {
				window.location = `create.php?url=${entry.url}`;
			} else {
				fetch(`enqueue.php?room=${roomKey}&url=${encodeURI(entry.url)}&title=${entry.title}`).then(resp => {
					window.location = `join.php?room=${roomKey}`;
				});
			}
		}
	});
}

window.addEventListener('load', function() {
	let url = new URL(window.location);
	let roomKey = url.searchParams.get('room');
	let series = url.searchParams.get('series');

	let homeButton = document.getElementById('home');
	let backButton = document.getElementById('back');
	let catalogDiv = document.getElementById('catalog');

	homeButton.addEventListener('click', function() {
		window.location = '.';
	});

	backButton.addEventListener('click', function() {
		window.location = 'browse.php';
	});

	if (series) {
		homeButton.style.display = 'none';
	} else {
		backButton.style.display = 'none';
	}

	let catalogUrl;

	if (series) {
		catalogUrl = `catalog.php?series=${series}`;
	} else {
		catalogUrl = 'catalog.php';
	}

	fetch(catalogUrl).then(resp => resp.json()).then(data => {
		if (series) {
			let playAllDiv = document.createElement('div');

			playAllDiv.className = 'catalog-entry';

			playAllDiv.innerHTML = '<div class="cover"><button><i class="fas fa-play"></i></button></div><div class="title">Play All</div>';

			addCatalogEntryClickListener(playAllDiv, {url: encodeURI("series=" + series)}, roomKey);

			catalogDiv.appendChild(playAllDiv);
		}

		data.forEach(entry => {
			let div = document.createElement('div');

			div.className = 'catalog-entry';

			addCatalogEntryClickListener(div, entry, roomKey);

			let coverDiv = document.createElement('div');
			coverDiv.className = 'cover';
			let coverImg = document.createElement('img');
			coverImg.src = entry.cover;
			coverDiv.appendChild(coverImg);
			div.appendChild(coverDiv);

			let titleDiv = document.createElement('div');
			titleDiv.className = 'title';
			titleDiv.innerHTML = entry.title;
			div.appendChild(titleDiv);

			catalogDiv.appendChild(div);
		});
	});
});
