<?php

$room = $_GET["room"];
$url = $_GET["url"];

$config = parse_ini_file("config.ini");

$conn = new mysqli($config["host"], $config["user"], $config["password"], $config["database"], $config["port"]);

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

$conn->close();

?>
