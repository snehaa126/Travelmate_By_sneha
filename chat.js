let socket;
let currentChatPartner;

function startChat(userId) {
    currentChatPartner = userId;
    document.getElementById('chat-container').style.display = 'flex';
    
    if (!socket || socket.readyState !== WebSocket.OPEN) {
        socket = new WebSocket('ws://your-websocket-server-url');

        socket.onopen = function(event) {
            console.log('WebSocket connection opened');
        };

        socket.onmessage = function(event) {
            const message = JSON.parse(event.data);
            displayMessage(message.sender, message.content);
        };

        socket.onclose = function(event) {
            console.log('WebSocket connection closed');
        };
    }
}

function sendMessage() {
    const input = document.getElementById('message-input');
    const message = input.value.trim();
    
    if (message && socket.readyState === WebSocket.OPEN) {
        const messageData = {
            sender: <?php echo $user_id; ?>,
            recipient: currentChatPartner,
            content: message
        };
        
        socket.send(JSON.stringify(messageData));
        displayMessage('You', message);
        input.value = '';
    }
}

function displayMessage(sender, content) {
    const messagesContainer = document.getElementById('chat-messages');
    const messageElement = document.createElement('div');
    messageElement.textContent = `${sender}: ${content}`;
    messagesContainer.appendChild(messageElement);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function searchProfiles() {
    const searchTerm = document.getElementById('citySearch').value.toLowerCase();
    const profileCards = document.querySelectorAll('.other-profile-card');
    
    profileCards.forEach(card => {
        const city = card.querySelector('p').textContent.toLowerCase();
        if (city.includes(searchTerm)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}