<?php
session_start();

$room = $_GET["room"];
$time = $_GET["time"];

$start_time = time() - $time;

$config = parse_ini_file("config.ini", true);

$conn = new mysqli($config["database"]["host"], $config["database"]["user"], $config["database"]["password"], $config["database"]["name"], $config["database"]["port"]);

$stmt = $conn->prepare("SELECT owner FROM room WHERE room_key = ?");
$stmt->bind_param("s", $room);
$stmt->bind_result($owner);
$stmt->execute();
$stmt->fetch();
$stmt->close();

if ($owner == null || session_id() == $owner) {
	$stmt = $conn->prepare("UPDATE room SET start_time = ? WHERE room_key = ?");
	$stmt->bind_param("is", $start_time, $room);
	$stmt->execute();
	$stmt->close();
}

$conn->close();
?>
