<?php
include "pmms.php";

session_start();

$room = $_GET["room"];
$url = $_GET["url"];
$title = $_GET["title"];

$conn = create_db_connection();

if (can_control_room($conn, session_id(), $room)) {
	$room_id = get_room_id($conn, $room);

	enqueue_video($conn, $room_id, $url, $title);
}

$conn->close();
?>
