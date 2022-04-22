<?php

$config = parse_ini_file("config.ini");

$room = $_GET["room"];

$conn = new mysqli($config["host"], $config["user"], $config["password"], $config["database"], $config["port"]);

$stmt = $conn->prepare("UPDATE room SET last_sync = UNIX_TIMESTAMP() WHERE room_key = ?");
$stmt->bind_param("s", $room);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("SELECT id, url, start_time, paused, loop_media FROM room WHERE room_key = ?");
$stmt->bind_param("s", $room);
$stmt->bind_result($room_id, $url, $start_time, $paused, $loop_media);
$stmt->execute();
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT id FROM queue WHERE room_id = ? ORDER BY id LIMIT 1");
$stmt->bind_param("i", $room_id);
$stmt->bind_result($queue_id);
$stmt->execute();
$stmt->fetch();
$stmt->close();

$conn->close();

$data = [
	"url" => $url,
	"start_time" => $start_time,
	"paused" => $paused,
	"loop" => $loop_media,
	"next" => $queue_id
];

header("Content-type: application/json");
echo json_encode($data);

?>
