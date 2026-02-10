/**
 * ChatRoom Application - Main JavaScript
 * Using: jQuery 3.7.1, Pusher 8.4.0, Bootstrap 5.3
 */

$(document).ready(function() {
    // Initialize Pusher with latest version
    const pusher = new Pusher('1617ae09d4a38695d623', {
        cluster: 'ap2',
        forceTLS: true,  // Updated from deprecated 'encrypted'
        enabledTransports: ['ws', 'wss']
    });

    const channel = pusher.subscribe('chat-channel');
    const $msgdiv = $("#msg-container");
    const $inputMsg = $("#input_msg");
    const $charCount = $("#char-count");

    // Load initial messages
    function loadMessages(shouldScroll = true) {
        $.ajax({
            url: 'readMsg.php',
            method: 'GET',
            cache: false,
            success: function(data) {
                $msgdiv.html(data);
                if (shouldScroll) {
                    scrollToBottom();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading messages:', error);
            }
        });
    }

    // Listen for new messages via Pusher
    channel.bind('new-message', function(data) {
        appendMessage(data);
        scrollToBottom();
    });

    // Append a single message to the chat (matches PHP styling exactly)
    function appendMessage(data) {
        const isSender = data.phone === currentUserPhone;
        const bgClass = isSender ? 'bg-primary text-white' : 'bg-light border';
        const alignClass = isSender ? 'ms-auto' : 'me-auto';
        const usernameColor = isSender ? 'text-white-50' : 'text-primary';
        const timeColor = isSender ? 'text-white-50' : 'text-muted';

        const messageHTML = `
            <div class="message p-2">
                <div class="d-flex ${isSender ? 'justify-content-end' : 'justify-content-start'}">
                    <div class="message-bubble ${bgClass} ${alignClass} rounded-3 p-3 mb-2 shadow-sm" style="max-width: 75%;">
                        <div class="message-header d-flex justify-content-between align-items-center mb-1">
                            <span class="username fw-bold ${usernameColor}" style="font-size: 0.85rem;">
                                ${escapeHtml(data.name)}
                            </span>
                            <span class="time ms-2 ${timeColor}" style="font-size: 0.75rem;">
                                ${data.time}
                            </span>
                        </div>
                        <div class="message-text" style="word-wrap: break-word;">${escapeHtml(data.message)}</div>
                    </div>
                </div>
            </div>
        `;

        $msgdiv.append(messageHTML);
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Scroll to bottom of chat
    function scrollToBottom() {
        $msgdiv.animate({
            scrollTop: $msgdiv[0].scrollHeight
        }, 300);
    }

    // Character counter
    $inputMsg.on('input', function() {
        const length = $(this).val().length;
        $charCount.text(`${length}/500`);
        
        if (length >= 450) {
            $charCount.addClass('text-warning');
        } else if (length >= 480) {
            $charCount.removeClass('text-warning').addClass('text-danger');
        } else {
            $charCount.removeClass('text-warning text-danger');
        }
    });

    // Send message on Enter key
    $inputMsg.on('keydown', function(e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Send button click
    $("#send-btn").on('click', function() {
        sendMessage();
    });

    // Send message function
    function sendMessage() {
        const msg = $inputMsg.val().trim();
        
        if (!msg) {
            $inputMsg.focus();
            return;
        }

        // Clear input immediately for better UX
        $inputMsg.val("");
        $charCount.text("0/500").removeClass('text-warning text-danger');

        // Show loading state on button
        const $btn = $("#send-btn");
        const originalText = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>')
            .prop('disabled', true);

        $.ajax({
            url: 'addMsg.php',
            method: 'GET',
            data: { msg: msg },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    console.log("Message sent successfully");
                } else {
                    console.error("Failed to send message:", response.message);
                    // Restore message if failed
                    $inputMsg.val(msg);
                    alert('Failed to send message: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error sending message:', error);
                console.error('Response:', xhr.responseText);
                
                // Try to parse error message from response
                let errorMsg = 'Error sending message. Please try again.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMsg = response.message;
                    }
                } catch (e) {
                    // If not JSON, use status text
                    if (xhr.statusText) {
                        errorMsg = 'Server error: ' + xhr.statusText;
                    }
                }
                
                // Restore message if failed
                $inputMsg.val(msg);
                alert(errorMsg);
            },
            complete: function() {
                // Restore button
                $btn.html(originalText).prop('disabled', false);
            }
        });
    }

    // Make sendMessage available globally for onclick
    window.update = sendMessage;

    // Connection status handling
    pusher.connection.bind('connected', function() {
        console.log('Pusher connected successfully');
    });

    pusher.connection.bind('disconnected', function() {
        console.log('Pusher disconnected');
    });

    pusher.connection.bind('error', function(err) {
        console.error('Pusher connection error:', err);
    });

    // Load messages on page load
    loadMessages();

    // Periodic fallback refresh (every 30 seconds as backup)
    setInterval(loadMessages, 30000);

    // Focus input on page load
    $inputMsg.focus();
});
