<?php

$room = $_GET["room"];
$time = $_GET["time"];

$config = parse_ini_file("config.ini");

$start_time = time() - $time;

$conn = new mysqli($config["host"], $config["user"], $config["password"], $config["database"], $config["port"]);

$stmt = $conn->prepare("UPDATE room SET start_time = ? WHERE room_key = ?");
$stmt->bind_param("is", $start_time, $room);
$stmt->execute();
$stmt->close();

$conn->close();

?>
