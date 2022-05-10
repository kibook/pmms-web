<?php
include "pmms.php";

session_start();

$room = $_GET["room"];
$time = $_GET["time"];

$start_time = time() - $time;

$conn = create_db_connection();

if (can_control_room($conn, session_id(), $room)) {
	$stmt = $conn->prepare("UPDATE room SET start_time = ? WHERE room_key = ?");
	$stmt->bind_param("is", $start_time, $room);
	$stmt->execute();
	$stmt->close();
}

$conn->close();
?>
