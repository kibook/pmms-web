<?php

$room = $_GET["room"];

$config = parse_ini_file("config.ini", true);

$conn = new mysqli($config["database"]["host"], $config["database"]["user"], $config["database"]["password"], $config["database"]["name"], $config["database"]["port"]);

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
