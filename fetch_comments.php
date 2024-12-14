<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db_config.php';

$apiKey = "AIzaSyCbbsrrMLtsfW8xClMmiXpqm766K5yvoxM";
$videoId = $_POST['video_id'] ?? ''; // Get video ID from frontend

if (!$videoId) {
    echo json_encode(["error" => "Video ID is required"]);
    exit;
}

$url = "https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&videoId=$videoId&key=$apiKey&maxResults=50";
$response = file_get_contents($url);
$commentsData = json_decode($response, true);

if (!isset($commentsData['items'])) {
    echo json_encode(["error" => "No comments found or invalid video ID"]);
    exit;
}

foreach ($commentsData['items'] as $item) {
    $commentId = $item['id'];
    $author = $item['snippet']['topLevelComment']['snippet']['authorDisplayName'];
    $text = $item['snippet']['topLevelComment']['snippet']['textOriginal'];

    $stmt = $pdo->prepare("INSERT INTO comments (video_id, comment_id, author, text) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE text = text");
    $stmt->execute([$videoId, $commentId, $author, $text]);
}

echo json_encode(["success" => true, "message" => "Comments fetched and stored"]);
?>
