<?php
session_start();

$room = $_GET["room"];
$queue_id = $_GET["id"];

$config = parse_ini_file("config.ini", true);

$conn = new mysqli($config["database"]["host"], $config["database"]["user"], $config["database"]["password"], $config["database"]["name"], $config["database"]["port"]);

$stmt = $conn->prepare("SELECT owner FROM room WHERE room_key = ?");
$stmt->bind_param("s", $room);
$stmt->bind_result($owner);
$stmt->execute();
$stmt->fetch();
$stmt->close();

if ($owner == null || session_id() == $owner) {
	if (isset($queue_id)) {
		$stmt = $conn->prepare("SELECT url FROM queue WHERE id = ?");
		$stmt->bind_param("i", $queue_id);
		$stmt->bind_result($url);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();

		if (isset($url)) {
			$stmt = $conn->prepare("UPDATE room SET url = ?, start_time = UNIX_TIMESTAMP() + 2, paused = null WHERE room_key = ?");
			$stmt->bind_param("ss", $url, $room);
			$stmt->execute();
			$stmt->close();

			$stmt = $conn->prepare("DELETE FROM queue WHERE id = ?");
			$stmt->bind_param("i", $queue_id);
			$stmt->execute();
			$stmt->close();
		}
	}
}

$conn->close();

?>
