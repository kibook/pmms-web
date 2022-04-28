<?php
session_start();

$room = `uuid`;
$url = $_GET["url"];
$lock = $_GET["lock"];

$config = parse_ini_file("config.ini", true);

$conn = new mysqli($config["database"]["host"], $config["database"]["user"], $config["database"]["password"], $config["database"]["name"], $config["database"]["port"]);

$stmt = $conn->prepare("DELETE FROM room WHERE UNIX_TIMESTAMP() - last_sync > ?");
$stmt->bind_param("i", $config["rooms"]["prune_after"]);
$stmt->execute();
$stmt->close();

$owner = $lock == "yes" ? session_id() : null;

$stmt = $conn->prepare("INSERT INTO room (room_key, url, start_time, last_sync, owner) VALUES (?, ?, UNIX_TIMESTAMP() + 2, UNIX_TIMESTAMP(), ?)");
$stmt->bind_param("sss", $room, $url, $owner);
$stmt->execute();
$stmt->close();

$conn->close();

header("Status: 302 Found");
header("Location: join.php?room=" . $room);
?>
