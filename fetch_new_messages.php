<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];
$other_user_id = isset($_GET['other_user_id']) ? intval($_GET['other_user_id']) : 0;
$last_message_id = isset($_GET['last_message_id']) ? intval($_GET['last_message_id']) : 0;

if ($other_user_id === 0) {
    header('HTTP/1.1 400 Bad Request');
    exit('Invalid other_user_id');
}

// Fetch new messages
$sql = "SELECT m.*, u.email as sender_email 
        FROM messages m 
        JOIN users u ON m.sender_id = u.id
        WHERE ((m.sender_id = ? AND m.receiver_id = ?) 
           OR (m.sender_id = ? AND m.receiver_id = ?))
           AND m.id > ?
        ORDER BY m.timestamp ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiii", $user_id, $other_user_id, $other_user_id, $user_id, $last_message_id);
$stmt->execute();
$result = $stmt->get_result();
$new_messages = $result->fetch_all(MYSQLI_ASSOC);

// Mark messages as read
$sql = "UPDATE messages SET is_read = TRUE 
        WHERE receiver_id = ? AND sender_id = ? AND is_read = FALSE";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $other_user_id);
$stmt->execute();

header('Content-Type: application/json');
echo json_encode($new_messages);