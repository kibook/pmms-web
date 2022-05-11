<?php
$Config = parse_ini_file("config.ini", true);

function create_db_connection() {
	global $Config;

	return new mysqli($Config["database"]["host"], $Config["database"]["user"], $Config["database"]["password"], $Config["database"]["name"], $Config["database"]["port"]);
}

function get_room_id($conn, $room) {
	$stmt = $conn->prepare("SELECT id FROM room WHERE room_key = ?");
	$stmt->bind_param("s", $room);
	$stmt->bind_result($room_id);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();

	return $room_id;
}

function get_youtube_playlist_id($url, &$id) {
	if (preg_match("/^(?:(?:https?:)?\/\/)?(?:(?:www|m)\.)?(?:youtube\.com|youtu.be)\/playlist\?list=(PL[a-z0-9_]+)$/i", $url, $match)) {
		$id = $match[1];
		return true;
	} else {
		return false;
	}
}

function enqueue_video($conn, $room_id, $url) {
	if (get_youtube_playlist_id($url, $playlist_id)) {
		return enqueue_youtube_playlist($conn, $room_id, $playlist_id);
	} else {
		$stmt = $conn->prepare("INSERT INTO queue (room_id, url) VALUES (?, ?)");
		$stmt->bind_param("is", $room_id, $url);
		$stmt->execute();
		$queue_id = $stmt->insert_id;
		$stmt->close();
		return $queue_id;
	}
}

function get_youtube_playlist_videos($playlist_id) {
	global $Config;

	if (!array_key_exists("api_key", $Config["youtube"])) {
		error_log("No YouTube API key specified in config.ini!");
		return [];
	}

	$video_ids = [];
	$next_page_token = false;

	while ($next_page_token !== null) {
		$url = "https://www.googleapis.com/youtube/v3/playlistItems?playlistId=" . $playlist_id . "&part=snippet&maxResults=50&key=" . $Config["youtube"]["api_key"];

		if ($next_page_token) {
			$url = $url . "&pageToken=" . $next_page_token;
		}

		$playlist = json_decode(file_get_contents($url));

		foreach ($playlist->items as $item) {
			array_push($video_ids, $item->snippet->resourceId->videoId);
		}

		if (property_exists($playlist, "nextPageToken")) {
			$next_page_token = $playlist->nextPageToken;
		} else {
			$next_page_token = null;
		}
	}

	return $video_ids;
}

function enqueue_youtube_playlist($conn, $room_id, $playlist_id) {
	$video_ids = get_youtube_playlist_videos($playlist_id);

	$queue_id = null;

	foreach ($video_ids as $video_id) {
		$id = enqueue_video($conn, $room_id, "https://youtube.com/watch?v=" . $video_id);

		if ($queue_id == null) {
			$queue_id = $id;
		}
	}

	return $queue_id;
}

function dequeue_video($conn, $room, $queue_id) {
	$stmt = $conn->prepare("SELECT url FROM queue WHERE id = ?");
	$stmt->bind_param("i", $queue_id);
	$stmt->bind_result($url);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();

	if (isset($url)) {
		$stmt = $conn->prepare("UPDATE room SET url = ?, start_time = UNIX_TIMESTAMP() + 2, paused = null WHERE room_key = ?");
		$stmt->bind_param("ss", $url, $room);
		$stmt->execute();
		$stmt->close();

		$stmt = $conn->prepare("DELETE FROM queue WHERE id = ?");
		$stmt->bind_param("i", $queue_id);
		$stmt->execute();
		$stmt->close();
	}
}

function get_owner($conn, $room) {
	$stmt = $conn->prepare("SELECT owner FROM room WHERE room_key = ?");
	$stmt->bind_param("s", $room);
	$stmt->bind_result($owner);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();

	return $owner;
}

function can_control_room($conn, $session_id, $room) {
	$owner = get_owner($conn, $room);

	return $owner == null || $session_id == $owner;
}
?>
