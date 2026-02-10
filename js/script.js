// Initialize Pusher
const pusher = new Pusher('1617ae09d4a38695d623', {
    cluster: 'ap2',
    encrypted: true
});

const channel = pusher.subscribe('chat-channel');

let msgdiv = document.querySelector(".msg");
let input_msg = document.getElementById("input_msg");

// Load initial messages
function loadMessages() {
    fetch("readMsg.php")
        .then(r => {
            if (r.ok) {
                return r.text();
            }
        })
        .then(d => {
            msgdiv.innerHTML = d;
            scrollToBottom();
        })
        .catch(err => console.error('Error loading messages:', err));
}

// Listen for new messages via Pusher
channel.bind('new-message', function(data) {
    appendMessage(data);
    scrollToBottom();
});

// Append a single message to the chat
function appendMessage(data) {
    const isSender = data.phone === currentUserPhone;
    const messageClass = isSender ? 'sender' : '';

    const messageHTML = `
        <div class="message ${messageClass}">
            <div class="message-header">
                <span class="username">${escapeHtml(data.name)}</span>
                <span class="time">${data.time}</span>
            </div>
            <div class="message-text">${escapeHtml(data.message)}</div>
        </div>
    `;

    msgdiv.insertAdjacentHTML('beforeend', messageHTML);
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Scroll to bottom of chat
function scrollToBottom() {
    msgdiv.scrollTop = msgdiv.scrollHeight;
}

// Send message on Enter key
window.onkeydown = (e) => {
    if (e.key === "Enter") {
        sendMessage();
    }
};

// Send message function
function sendMessage() {
    let msg = input_msg.value.trim();
    if (!msg) return;

    input_msg.value = "";

    fetch(`addMsg.php?msg=${encodeURIComponent(msg)}`)
        .then(r => {
            if (r.ok) {
                return r.json();
            }
        })
        .then(d => {
            if (d.status === 'success') {
                console.log("Message sent successfully");
            } else {
                console.error("Failed to send message:", d.message);
            }
        })
        .catch(err => console.error('Error sending message:', err));
}

// Make sendMessage available globally for onclick
window.update = sendMessage;

// Load messages on page load
loadMessages();

// Periodic fallback refresh (every 10 seconds as backup)
setInterval(loadMessages, 10000);
