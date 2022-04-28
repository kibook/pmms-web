<?php
session_start();

$room = $_GET["room"];
$loop = $_GET["loop"];
$time = $_GET["time"];

$config = parse_ini_file("config.ini", true);

$conn = new mysqli($config["database"]["host"], $config["database"]["user"], $config["database"]["password"], $config["database"]["name"], $config["database"]["port"]);

$stmt = $conn->prepare("SELECT owner FROM room WHERE room_key = ?");
$stmt->bind_param("s", $room);
$stmt->bind_result($owner);
$stmt->execute();
$stmt->fetch();
$stmt->close();

if ($owner == null || session_id() == $owner) {
	if ($loop == "yes") {
		$stmt = $conn->prepare("UPDATE room SET loop_media = true WHERE room_key = ?");
		$stmt->bind_param("s", $room);
		$stmt->execute();
		$stmt->close();
	} else {
		$stmt = $conn->prepare("UPDATE room SET loop_media = false, start_time = UNIX_TIMESTAMP() - ? WHERE room_key = ?");
		$stmt->bind_param("is", $time, $room);
		$stmt->execute();
		$stmt->close();
	}
}

$conn->close();
?>
