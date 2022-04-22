<?php

$config = parse_ini_file("config.ini");

$room = $_GET["room"];

$conn = new mysqli($config["host"], $config["user"], $config["password"], $config["database"], $config["port"]);

$stmt = $conn->prepare("SELECT queue.id AS id, queue.url AS url FROM room, queue WHERE room.id = queue.room_id AND room.room_key = ?");
$stmt->bind_param("s", $room);
$stmt->bind_result($queue_id, $url);
$stmt->execute();

$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
	$data[] = [
		"id" => $row["id"],
		"url" => $row["url"]
	];
}

$stmt->close();

$conn->close();

header("Content-type: application/json");
echo json_encode($data);

?>
