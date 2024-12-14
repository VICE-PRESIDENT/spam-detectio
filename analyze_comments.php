<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db_config.php';

$videoId = $_POST['video_id'] ?? '';

if (!$videoId) {
    echo json_encode(["error" => "Video ID is required"]);
    exit;
}

// Simple rule-based spam detection (can be replaced with ML API integration)
$spamWords = ["fuck", "nude", "money", "share", "spam","happy"];
$query = "SELECT id, text FROM comments WHERE video_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$videoId]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($comments as $comment) {
    $isSpam = 0;
    foreach ($spamWords as $word) {
        if (stripos($comment['text'], $word) !== false) {
            $isSpam = 1;
            break;
        }
    }
    $updateStmt = $pdo->prepare("UPDATE comments SET is_spam = ? WHERE id = ?");
    $updateStmt->execute([$isSpam, $comment['id']]);
}

echo json_encode(["success" => true, "message" => "Spam analysis complete"]);
?>
