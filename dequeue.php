<?php
include "pmms.php";

session_start();

$room = $_GET["room"];
$queue_id = $_GET["id"];

$conn = create_db_connection();

if (can_control_room($conn, session_id(), $room)) {
	dequeue_video($conn, $room, $queue_id);
}

$conn->close();

?>
