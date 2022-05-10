<?php
include "pmms.php";

session_start();

$room = $_GET["room"];

$conn = create_db_connection();

$owner = get_owner($conn, $room);

$session_id = session_id();

if ($session_id == $owner) {
	$stmt = $conn->prepare("UPDATE room SET owner = NULL WHERE room_key = ?");
	$stmt->bind_param("s", $room);
	$stmt->execute();
	$stmt->close();
} else if ($owner == null) {
	$stmt = $conn->prepare("UPDATE room SET owner = ? WHERE room_key = ?");
	$stmt->bind_param("ss", $session_id, $room);
	$stmt->execute();
	$stmt->close();
}

$conn->close();
?>
