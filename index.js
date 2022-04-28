window.addEventListener('load', function() {
	let lock = document.getElementById('lock');

	document.getElementById('lock-btn').addEventListener('click', function() {
		if (lock.value == "yes") {
			this.innerHTML = '<i class="fas fa-lock-open"></i>';
			lock.value = "no";
		} else {
			this.innerHTML = '<i class="fas fa-lock"></i>';
			lock.value = "yes";
		}
	});
});
