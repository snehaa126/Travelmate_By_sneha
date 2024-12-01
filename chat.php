<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$other_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($other_user_id === 0) {
    header("Location: demo7.php");
    exit();
}

// Fetch other user's information
$sql = "SELECT email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $other_user_id);
$stmt->execute();
$result = $stmt->get_result();
$other_user = $result->fetch_assoc();

// Check if $other_user is null before accessing its elements
if ($other_user === null) {
    $_SESSION['error_message'] = "User not found. Please select a valid user to chat with.";
    header("Location: demo7.php");
    exit();
}

// Fetch chat messages
$sql = "SELECT m.*, u.email as sender_email 
        FROM messages m 
        JOIN users u ON m.sender_id = u.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) 
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.timestamp ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user_id, $other_user_id, $other_user_id, $user_id);
$stmt->execute();
$messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

function getUsername($email) {
    $parts = explode('@', $email);
    return $parts[0];
}

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message, is_read) VALUES (?, ?, ?, FALSE)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $user_id, $other_user_id, $message);
        $stmt->execute();
        
        // Instead of redirecting, we'll fetch the newly inserted message
        $new_message_id = $conn->insert_id;
        $sql = "SELECT m.*, u.email as sender_email 
                FROM messages m 
                JOIN users u ON m.sender_id = u.id
                WHERE m.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $new_message_id);
        $stmt->execute();
        $new_message = $stmt->get_result()->fetch_assoc();
        
        // Return the new message as JSON
        header('Content-Type: application/json');
        echo json_encode($new_message);
        exit();
    }
}

// Mark messages as read
$sql = "UPDATE messages SET is_read = TRUE 
        WHERE receiver_id = ? AND sender_id = ? AND is_read = FALSE";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $other_user_id);
$stmt->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars(getUsername($other_user['email'])); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .chat-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chat-header {
            background-color: #faa8a8;
            color: #35424a;
            padding: 10px 20px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
        }
        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            max-width: 70%;
        }
        .message.sent {
            background-color: #e3f2fd;
            margin-left: auto;
        }
        .message.received {
            background-color: #f3e5f5;
        }
        .chat-input {
            padding: 20px;
            border-top: 1px solid #eee;
        }
        .chat-input form {
            display: flex;
        }
        .chat-input input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .chat-input button {
            padding: 10px 20px;
            background-color: #faa8a8;
            color: #35424a;
            border: none;
            border-radius: 4px;
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h2>Chat with <?php echo htmlspecialchars(getUsername($other_user['email'])); ?></h2>
        </div>
        <div class="chat-messages" id="chatMessages">
            <?php foreach ($messages as $message): ?>
                <div class="message <?php echo $message['sender_id'] == $user_id ? 'sent' : 'received'; ?>" data-message-id="<?php echo $message['id']; ?>">
                    <strong><?php echo htmlspecialchars(getUsername($message['sender_email'])); ?>:</strong>
                    <?php echo htmlspecialchars($message['message']); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="chat-input">
            <form id="messageForm" method="POST">
                <input type="text" name="message" id="messageInput" placeholder="Type your message..." required>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
    <script>
        const chatMessages = document.getElementById('chatMessages');
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');

        // Scroll to bottom of chat messages
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        scrollToBottom();

        // Function to add a new message to the chat
        function addMessageToChat(message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${message.sender_id == <?php echo $user_id; ?> ? 'sent' : 'received'}`;
            messageDiv.dataset.messageId = message.id;
            messageDiv.innerHTML = `<strong>${getUsername(message.sender_email)}:</strong> ${message.message}`;
            chatMessages.appendChild(messageDiv);
            scrollToBottom();
        }

        // Function to fetch new messages
        function fetchNewMessages() {
            const lastMessageId = getLastMessageId();
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const newMessages = JSON.parse(this.responseText);
                    newMessages.forEach(message => {
                        if (!document.querySelector(`.message[data-message-id="${message.id}"]`)) {
                            addMessageToChat(message);
                        }
                    });
                }
            };
            xhr.open("GET", `fetch_new_messages.php?other_user_id=<?php echo $other_user_id; ?>&last_message_id=${lastMessageId}`, true);
            xhr.send();
        }

        function getLastMessageId() {
            const messages = document.querySelectorAll('.message');
            return messages.length > 0 ? messages[messages.length - 1].dataset.messageId : 0;
        }

        function getUsername(email) {
            return email.split('@')[0];
        }

        // Handle form submission
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (message) {
                const xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        const newMessage = JSON.parse(this.responseText);
                        addMessageToChat(newMessage);
                        messageInput.value = '';
                    }
                };
                xhr.open("POST", "chat.php?user_id=<?php echo $other_user_id; ?>", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.send(`message=${encodeURIComponent(message)}`);
            }
        });

        // Fetch new messages every 5 seconds
        setInterval(fetchNewMessages, 5000);
    </script>
</body>
</html>