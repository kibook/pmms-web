<?php
include "pmms.php";

session_start();

$url = get_param("url");
$lock = get_param("lock");

$conn = create_db_connection();

if ($Config["rooms"]["lock_by_default"]) {
	$locked = $lock == "no" ? 0 : 1;
} else {
	$locked = $lock == "yes" ? 1 : 0;
}

$room = create_room($conn, $url, $locked);

$conn->close();

header("Status: 302 Found");
header("Location: join.php?room=" . $room);
?>
