<?php
include "pmms.php";

session_start();

$room = $_GET["room"];
$loop = $_GET["loop"];
$time = $_GET["time"];

$conn = create_db_connection();

if (can_control_room($conn, session_id(), $room)) {
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
}

$conn->close();
?>
