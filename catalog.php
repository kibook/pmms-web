<?php
include "pmms.php";

$conn = create_db_connection();

if (isset($_GET["query"])) {
	$query = implode("* ", explode(" ", $_GET["query"])) . "*";

	if (isset($_GET["series"])) {
		if (isset($_GET["category"])) {
			$stmt = $conn->prepare("SELECT id, url, title, cover, MATCH (sort_title, keywords) AGAINST (? IN BOOLEAN MODE) AS relevance, LENGTH(sort_title) AS length FROM catalog WHERE series = ? AND category = ? AND MATCH (sort_title, keywords) AGAINST (? IN BOOLEAN MODE) AND hidden = FALSE ORDER BY relevance DESC, length, sort_title");
			$stmt->bind_param("siss", $query, $_GET["series"], $_GET["category"], $query);
		} else {
			$stmt = $conn->prepare("SELECT id, url, title, cover, MATCH (sort_title, keywords) AGAINST (? IN BOOLEAN MODE) AS relevance, LENGTH(sort_title) AS length FROM catalog WHERE series = ? AND MATCH (sort_title, keywords) AGAINST (? IN BOOLEAN MODE) AND hidden = FALSE ORDER BY relevance DESC, length, sort_title");
			$stmt->bind_param("sis", $query, $_GET["series"], $query);
		}
	} else {
		if (isset($_GET["category"])) {
			$stmt = $conn->prepare("SELECT id, url, title, cover, MATCH (sort_title, keywords) AGAINST (? IN BOOLEAN MODE) AS relevance, LENGTH(sort_title) AS length FROM catalog WHERE category = ? AND MATCH (sort_title, keywords) AGAINST (? IN BOOLEAN MODE) AND hidden = FALSE ORDER BY relevance DESC, length, sort_title");
			$stmt->bind_param("sss", $query, $_GET["category"], $query);
		} else {
			$stmt = $conn->prepare("SELECT id, url, title, cover, MATCH (sort_title, keywords) AGAINST (? IN BOOLEAN MODE) AS relevance, LENGTH(sort_title) AS length FROM catalog WHERE MATCH (sort_title, keywords) AGAINST (? IN BOOLEAN MODE) AND hidden = FALSE ORDER BY relevance DESC, length, sort_title");
			$stmt->bind_param("ss", $query, $query);
		}
	}
} else {
	if (isset($_GET["series"])) {
		if (isset($_GET["category"])) {
			$stmt = $conn->prepare("SELECT id, url, title, cover FROM catalog WHERE series = ? AND category = ? AND hidden = FALSE ORDER BY sort_title");
			$stmt->bind_param("is", $_GET["series"], $_GET["category"]);
		} else {
			$stmt = $conn->prepare("SELECT id, url, title, cover FROM catalog WHERE series = ? AND hidden = FALSE ORDER BY sort_title");
			$stmt->bind_param("i", $_GET["series"]);
		}
	} else {
		if (isset($_GET["category"])) {
			$stmt = $conn->prepare("SELECT id, url, title, cover FROM catalog WHERE series IS NULL AND category = ? AND hidden = FALSE ORDER BY sort_title");
			$stmt->bind_param("s", $_GET["category"]);
		} else {
			$stmt = $conn->prepare("SELECT id, url, title, cover FROM catalog WHERE series IS NULL AND hidden = FALSE ORDER BY sort_title");
		}
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
