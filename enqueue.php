<?php
session_start();

$room = $_GET["room"];
$url = $_GET["url"];

$config = parse_ini_file("config.ini", true);

$conn = new mysqli($config["database"]["host"], $config["database"]["user"], $config["database"]["password"], $config["database"]["name"], $config["database"]["port"]);

$stmt = $conn->prepare("SELECT owner FROM room WHERE room_key = ?");
$stmt->bind_param("s", $room);
$stmt->bind_result($owner);
$stmt->execute();
$stmt->fetch();
$stmt->close();

if ($owner == null || session_id() == $owner) {
	$stmt = $conn->prepare("SELECT id FROM room WHERE room_key = ?");
	$stmt->bind_param("s", $room);
	$stmt->bind_result($room_id);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();

	$stmt = $conn->prepare("INSERT INTO queue (room_id, url) VALUES (?, ?)");
	$stmt->bind_param("is", $room_id, $url);
	$stmt->execute();
	$stmt->close();
}

$conn->close();
?>
