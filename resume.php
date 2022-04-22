<?php
$room = $_GET["room"];

$config = parse_ini_file("config.ini");

$conn = new mysqli($config["host"], $config["user"], $config["password"], $config["database"], $config["port"]);

$stmt = $conn->prepare("UPDATE room SET start_time = start_time + (UNIX_TIMESTAMP() - paused), paused = null WHERE room_key = ?");
$stmt->bind_param("s", $room);
$stmt->execute();
$stmt->close();

$conn->close();

?>
