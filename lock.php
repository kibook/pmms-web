<?php
include "pmms.php";

session_start();

$room = $_GET["room"];

$conn = create_db_connection();

$stmt = $conn->prepare("SELECT owner, locked FROM room WHERE room_key = ?");
$stmt->bind_param("s", $room);
$stmt->bind_result($owner, $locked);
$stmt->execute();
$stmt->fetch();
$stmt->close();

if (session_id() == $owner) {
	$lock = $locked ? 0 : 1;

	$stmt = $conn->prepare("UPDATE room SET locked = ? WHERE room_key = ?");
	$stmt->bind_param("is", $lock, $room);
	$stmt->execute();
	$stmt->close();
}

$conn->close();
?>
