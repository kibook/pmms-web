<?php
include "pmms.php";

$series = get_param("series");

$conn = create_db_connection();

if (isset($series)) {
	$stmt = $conn->prepare("SELECT id, url, title, cover FROM catalog WHERE series = ? ORDER BY title");
	$stmt->bind_param("i", $series);
} else {
	$stmt = $conn->prepare("SELECT id, url, title, cover FROM catalog WHERE series IS NULL ORDER BY title");
}

$stmt->execute();

$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
	$data[] = [
		"id" => $row["id"],
		"url" => $row["url"],
		"title" => $row["title"],
		"cover" => $row["cover"]
	];
}

$stmt->close();
$conn->close();

header("Content-type: application/json");
echo json_encode($data);
?>
