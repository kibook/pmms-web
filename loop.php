<?php
$room = $_GET["room"];
$loop = $_GET["loop"];
$time = $_GET["time"];

$config = parse_ini_file("config.ini");

$conn = new mysqli($config["host"], $config["user"], $config["password"], $config["database"], $config["port"]);

if ($loop == "yes") {
	$stmt = $conn->prepare("UPDATE room SET loop_media = true WHERE room_key = ?");
	$stmt->bind_param("s", $room);
	$stmt->execute();
	$stmt->close();
} else {
	$stmt = $conn->prepare("UPDATE room SET loop_media = false, start_time = UNIX_TIMESTAMP() - ? WHERE room_key = ?");
	$stmt->bind_param("is", $time, $room);
	$stmt->execute();
	$stmt->close();
}

$conn->close();

?>
