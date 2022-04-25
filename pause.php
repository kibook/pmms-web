<?php
$room = $_GET["room"];

$config = parse_ini_file("config.ini", true);

$conn = new mysqli($config["database"]["host"], $config["database"]["user"], $config["database"]["password"], $config["database"]["name"], $config["database"]["port"]);

$stmt = $conn->prepare("UPDATE room SET paused = UNIX_TIMESTAMP() WHERE room_key = ?");
$stmt->bind_param("s", $room);
$stmt->execute();
$stmt->close();

$conn->close();

?>
