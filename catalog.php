<?php
include "pmms.php";

$conn = create_db_connection();

if (isset($_GET["series"])) {
	if (isset($_GET["category"])) {
		$stmt = $conn->prepare("SELECT id, url, title, cover FROM catalog WHERE series = ? AND category = ? ORDER BY sort_title");
		$stmt->bind_param("is", $_GET["series"], $_GET["category"]);
	} else {
		$stmt = $conn->prepare("SELECT id, url, title, cover FROM catalog WHERE series = ? ORDER BY sort_title");
		$stmt->bind_param("i", $_GET["series"]);
	}
} else {
	if (isset($_GET["category"])) {
		$stmt = $conn->prepare("SELECT id, url, title, cover FROM catalog WHERE series IS NULL AND category = ? ORDER BY sort_title");
		$stmt->bind_param("s", $_GET["category"]);
	} else {
		$stmt = $conn->prepare("SELECT id, url, title, cover FROM catalog WHERE series IS NULL ORDER BY sort_title");
	}
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
