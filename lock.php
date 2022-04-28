<?php
session_start();

$room = $_GET["room"];

$config = parse_ini_file("config.ini", true);

$conn = new mysqli($config["database"]["host"], $config["database"]["user"], $config["database"]["password"], $config["database"]["name"], $config["database"]["port"]);

$stmt = $conn->prepare("SELECT owner FROM room WHERE room_key = ?");
$stmt->bind_param("s", $room);
$stmt->bind_result($owner);
$stmt->execute();
$stmt->fetch();
$stmt->close();

if (session_id() == $owner) {
	$stmt = $conn->prepare("UPDATE room SET owner = NULL WHERE room_key = ?");
	$stmt->bind_param("s", $room);
	$stmt->execute();
	$stmt->close();
} else if ($owner == null) {
	$stmt = $conn->prepare("UPDATE room SET owner = ? WHERE room_key = ?");
	$stmt->bind_param("ss", session_id(), $room);
	$stmt->execute();
	$stmt->close();
}

$conn->close();
?>
