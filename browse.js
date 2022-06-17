window.addEventListener('load', function() {
	let url = new URL(window.location);
	let roomKey = url.searchParams.get('room');
	let series = url.searchParams.get('series');
	let category = url.searchParams.get('category');

	let homeButton = document.getElementById('home');
	let backButton = document.getElementById('back');
	let catalogDiv = document.getElementById('catalog');

	let allButton = document.getElementById('category-all');
	let movieButton = document.getElementById('category-movie');
	let tvButton = document.getElementById('category-tv');
	let musicButton = document.getElementById('category-music');

	homeButton.addEventListener('click', function() {
		window.location = '.';
	});

	backButton.addEventListener('click', function() {
		url.searchParams.delete('series');
		window.location = url.toString();
	});

	allButton.addEventListener('click', function() {
		url.searchParams.delete('category');
		window.location = url.toString();
	});

	movieButton.addEventListener('click', function() {
		url.searchParams.set('category', 'movie');
		window.location = url.toString();
	});

	tvButton.addEventListener('click', function() {
		url.searchParams.set('category', 'tv');
		window.location = url.toString();
	});

	musicButton.addEventListener('click', function() {
		url.searchParams.set('category', 'music');
		window.location = url.toString();
	});

	switch (category) {
		case 'movie':
			allButton.style.color = 'grey';
			movieButton.style.color = 'black';
			tvButton.style.color = 'grey';
			musicButton.style.color = 'grey';
			break;
		case 'tv':
			allButton.style.color = 'grey';
			movieButton.style.color = 'grey';
			tvButton.style.color = 'black';
			musicButton.style.color = 'grey';
			break;
		case 'music':
			allButton.style.color = 'grey';
			movieButton.style.color = 'grey';
			tvButton.style.color = 'grey';
			musicButton.style.color = 'black';
			break;
		default:
			allButton.style.color = 'black';
			movieButton.style.color = 'grey';
			tvButton.style.color = 'grey';
			musicButton.style.color = 'grey';
			break;
	}

	if (series) {
		homeButton.style.display = 'none';
		allButton.style.display = 'none';
		movieButton.style.display = 'none';
		tvButton.style.display = 'none';
		musicButton.style.display = 'none';
	} else {
		backButton.style.display = 'none';
	}

	function addCatalogEntryClickListener(div, entry) {
		div.addEventListener('click', () => {
			if (entry.url == null) {
				url.searchParams.set('series', entry.id);
				window.location = url.toString();
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

	let catalogUrl = 'catalog.php?' + url.searchParams.toString();

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
