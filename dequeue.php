<?php

$room = $_GET["room"];
$queue_id = $_GET["id"];

$config = parse_ini_file("config.ini");

$conn = new mysqli($config["host"], $config["user"], $config["password"], $config["database"], $config["port"]);

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

$conn->close();

?>
