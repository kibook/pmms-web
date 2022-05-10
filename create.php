<?php
include "pmms.php";

session_start();

$url = $_GET["url"];
$lock = $_GET["lock"];

$conn = create_db_connection();

$stmt = $conn->prepare("DELETE FROM room WHERE UNIX_TIMESTAMP() - last_sync > ?");
$stmt->bind_param("i", $Config["rooms"]["prune_after"]);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("SELECT UUID()");
$stmt->bind_result($room);
$stmt->execute();
$stmt->fetch();
$stmt->close();

$owner = $lock == "yes" ? session_id() : null;

$stmt = $conn->prepare("INSERT INTO room (room_key, start_time, last_sync, owner) VALUES (?, UNIX_TIMESTAMP() + 2, UNIX_TIMESTAMP(), ?)");
$stmt->bind_param("ss", $room, $owner);
$result = $stmt->execute();
$stmt->close();

$room_id = get_room_id($conn, $room);
$queue_id = enqueue_video($conn, $room_id, $url);
dequeue_video($conn, $room, $queue_id);

$conn->close();

header("Status: 302 Found");
header("Location: join.php?room=" . $room);
?>
