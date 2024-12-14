<?php
include 'db_config.php';

$videoId = $_GET['video_id'] ?? '';

if (!$videoId) {
    echo json_encode(["error" => "Video ID is required"]);
    exit;
}

$query = "SELECT id, author, text, is_spam FROM comments WHERE video_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$videoId]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Modify the response format
$formattedComments = [];
foreach ($comments as $comment) {
    $formattedComments[] = [
        "id" => $comment['id'],
        "author" => $comment['author'],
        "text" => $comment['text'],
        "is_spam" => $comment['is_spam']
    ];
}

echo json_encode($formattedComments);
?>
