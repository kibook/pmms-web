<?php
include "pmms.php";

session_start();

$room = $_GET["room"];

$conn = create_db_connection();

if (can_control_room($conn, session_id(), $room)) {
	if ($is_public) {
		$stmt = $conn->prepare("UPDATE room SET is_public = FALSE WHERE room_key = ?");
	} else {
		$stmt = $conn->prepare("UPDATE room SET is_public = TRUE WHERE room_key = ?");
	}

	$stmt->bind_param("s", $room);
	$stmt->execute();
	$stmt->close();
}

$conn->close();
?>
