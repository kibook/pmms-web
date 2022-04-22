<?php

$room = `uuid`;
$url = $_GET["url"];

$config = parse_ini_file("config.ini");

$conn = new mysqli($config["host"], $config["user"], $config["password"], $config["database"], $config["port"]);

$conn->query("DELETE FROM room WHERE UNIX_TIMESTAMP() - last_sync > 60");

$stmt = $conn->prepare("INSERT INTO room (room_key, url, start_time) VALUES (?, ?, UNIX_TIMESTAMP() + 2)");
$stmt->bind_param("ss", $room, $url);

$stmt->execute();

$stmt->close();
$conn->close();

header("Status: 302 Found");
header("Location: join.html?room=" . $room);

?>
