<?php

$room = `uuid`;
$url = $_GET["url"];

$config = parse_ini_file("config.ini", true);

$conn = new mysqli($config["database"]["host"], $config["database"]["user"], $config["database"]["password"], $config["database"]["name"], $config["database"]["port"]);

$stmt = $conn->prepare("DELETE FROM room WHERE UNIX_TIMESTAMP() - last_sync > ?");
$stmt->bind_param("i", $config["rooms"]["prune_after"]);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("INSERT INTO room (room_key, url, start_time, last_sync) VALUES (?, ?, UNIX_TIMESTAMP() + 2, UNIX_TIMESTAMP())");
$stmt->bind_param("ss", $room, $url);
$stmt->execute();
$stmt->close();

$conn->close();

header("Status: 302 Found");
header("Location: join.html?room=" . $room);

?>
