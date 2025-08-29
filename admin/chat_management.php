<?php
// Page-specific data
$page_title = "RAIS Admin - Chat Management";
$active_page = "chat_management"; // For highlighting the active link in the sidebar
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap"
        rel="stylesheet">
    <link rel="icon" href="../img/logoulit.png" />
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="main-wrapper">
        
        <?php require_once 'sidebar.php'; ?>

        <div class="content-area">
            
            <?php require_once 'header.php'; ?>

            <main class="main-content main-content-chat">
                <h1>Chat Management</h1>

                <div class="chat-container-admin">
                    <div class="conversation-list-panel">
                        <div class="conversation-search">
                            <input type="text" class="form-control" placeholder="Search conversations..." id="searchConversationsInput">
                        </div>
                        <div id="conversationList">
                            <!-- Conversation list is rendered by JavaScript -->
                            <div class="text-center p-3">Loading conversations...</div>
                        </div>
                    </div>

                    <div class="chat-area-panel">
                        <div class="chat-header-panel d-flex align-items-center">
                            <button class="btn btn-link d-md-none me-2" id="backToConversations"><i class="bi bi-arrow-left fs-5"></i></button>
                            <span id="activeChatUserName">Select a conversation</span>
                        </div>
                        <div class="chat-messages" id="chatMessages">
                             <!-- Messages are rendered by JavaScript -->
                             <div class="text-center text-muted p-5">Please select a conversation to view messages.</div>
                        </div>
                        <div class="chat-input-area">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Reply..." id="adminMessageInput" disabled>
                                <button class="btn" type="button" id="sendAdminMessage" disabled>Send</button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- MODALS -->
    <div class="modal fade" id="deleteChatModal" tabindex="-1" aria-labelledby="deleteChatModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteChatModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="deleteChatModalBody">
                    Are you sure you want to delete this conversation?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteChatButton">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="togglemodeScript.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            // --- STATE MANAGEMENT ---
            let conversations = {};
            let activeUserId = null;
            let pollingInterval = null;

            // --- DOM Element Selectors ---
            const chatContainer = document.querySelector('.chat-container-admin');
            const conversationListContainer = document.getElementById('conversationList');
            const chatMessagesContainer = document.getElementById('chatMessages');
            const activeChatUserName = document.getElementById('activeChatUserName');
            const adminMessageInput = document.getElementById('adminMessageInput');
            const sendAdminMessageBtn = document.getElementById('sendAdminMessage');
            const backToConversationsBtn = document.getElementById('backToConversations');
            const searchInput = document.getElementById('searchConversationsInput');
            const deleteChatModal = document.getElementById('deleteChatModal');

            // --- API FUNCTIONS ---
            async function loadConversations() {
                try {
                    // CORRECTED PATH: ../api/
                    const response = await fetch('../api/get_conversations.php');
                    if (!response.ok) throw new Error('Network response was not ok');
                    
                    const data = await response.json();
                    conversations = data;
                    
                    const userIds = Object.keys(conversations);
                    if (!activeUserId && userIds.length > 0) {
                        activeUserId = userIds[0];
                    }
                    
                    renderConversationList();
                } catch (error) {
                    console.error('Failed to load conversations:', error);
                    conversationListContainer.innerHTML = '<div class="text-danger p-3">Could not load conversations.</div>';
                }
            }

            function renderConversationList(filteredConversations = conversations) {
                conversationListContainer.innerHTML = '';
                const userIds = Object.keys(filteredConversations);

                if (userIds.length === 0) {
                    conversationListContainer.innerHTML = '<div class="text-muted p-3">No conversations found.</div>';
                    return;
                }

                for (const userId in filteredConversations) {
                    const convo = filteredConversations[userId];
                    const conversationItem = document.createElement('div');
                    conversationItem.classList.add('conversation-item');
                    if (userId == activeUserId) { // Use == for loose comparison as one might be a string
                        conversationItem.classList.add('active');
                    }
                    conversationItem.setAttribute('data-user-id', userId);
                    
                    conversationItem.innerHTML = `
                        <img src="${convo.avatar}" alt="User Avatar" onerror="this.src='https://placehold.co/40x40/D9D9D9/525252?text=??'">
                        <div class="details">
                            <div class="name">${convo.name}</div>
                        </div>
                        <button class="btn btn-sm btn-outline-danger delete-convo-btn ms-auto" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteChatModal" 
                                data-user-id="${userId}" 
                                data-user-name="${convo.name}">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                    
                    conversationItem.addEventListener('click', (e) => {
                        if (e.target.closest('.delete-convo-btn')) return; 
                        switchActiveChat(userId);
                    });
                    conversationListContainer.appendChild(conversationItem);
                }
            }

            async function renderChatMessages() {
                if (!activeUserId) {
                    chatMessagesContainer.innerHTML = '<div class="text-center text-muted p-5">Please select a conversation to view messages.</div>';
                    activeChatUserName.textContent = 'Select a conversation';
                    adminMessageInput.placeholder = 'No conversation selected...';
                    adminMessageInput.disabled = true;
                    sendAdminMessageBtn.disabled = true;
                    return;
                }

                adminMessageInput.disabled = false;
                sendAdminMessageBtn.disabled = false;
                activeChatUserName.textContent = conversations[activeUserId]?.name || 'Loading...';
                adminMessageInput.placeholder = `Reply to ${conversations[activeUserId]?.name}...`;

                try {
                    // CORRECTED PATH: ../api/
                    const response = await fetch(`../api/get_messages.php?user_id=${activeUserId}`);
                    if (!response.ok) throw new Error('Failed to fetch messages');
                    
                    const messages = await response.json();
                    chatMessagesContainer.innerHTML = '';

                    if (messages.length === 0) {
                        chatMessagesContainer.innerHTML = '<div class="text-center text-muted p-5">No messages yet. Start the conversation!</div>';
                    } else {
                        messages.forEach(message => {
                            const messageBubble = document.createElement('div');
                            messageBubble.classList.add('chat-message-bubble', message.sender);
                            messageBubble.innerHTML = `
                                <div class="sender-name">${message.sender === 'user' ? conversations[activeUserId].name : 'Admin'}</div>
                                <div class="chat-message-content">${message.text}</div>
                                <span class="timestamp">${message.timestamp}</span>
                            `;
                            chatMessagesContainer.appendChild(messageBubble);
                        });
                    }
                    chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
                } catch (error) {
                    console.error('Error fetching messages:', error);
                    chatMessagesContainer.innerHTML = '<div class="text-danger p-3">Could not load messages.</div>';
                }
            }

            function switchActiveChat(userId) {
                activeUserId = userId;
                renderConversationList();
                renderChatMessages();
                chatContainer.classList.add('view-active');
            }

            async function sendAdminMessage() {
                const messageText = adminMessageInput.value.trim();
                if (!messageText || !activeUserId) return;

                const messageData = {
                    sender_id: 0,
                    receiver_id: parseInt(activeUserId),
                    message: messageText
                };

                try {
                    // CORRECTED PATH: ../api/
                    const response = await fetch('../api/send_message.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(messageData)
                    });

                    if (!response.ok) throw new Error('Failed to send message');

                    adminMessageInput.value = '';
                    await renderChatMessages();
                } catch (error) {
                    console.error('Error sending message:', error);
                }
            }

            function startPolling() {
                if (pollingInterval) clearInterval(pollingInterval);
                pollingInterval = setInterval(async () => {
                    await loadConversations();
                    if (activeUserId) {
                        await renderChatMessages();
                    }
                }, 5000);
            }

            // --- EVENT LISTENERS ---
            sendAdminMessageBtn.addEventListener('click', sendAdminMessage);
            adminMessageInput.addEventListener('keypress', (e) => e.key === 'Enter' && sendAdminMessage());
            backToConversationsBtn.addEventListener('click', () => chatContainer.classList.remove('view-active'));

            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const filtered = Object.keys(conversations).reduce((acc, userId) => {
                    if (conversations[userId].name.toLowerCase().includes(searchTerm)) {
                        acc[userId] = conversations[userId];
                    }
                    return acc;
                }, {});
                renderConversationList(filtered);
            });

            // (Modal logic remains unchanged)

            // --- INITIALIZATION ---
            await loadConversations();
            if (activeUserId) {
                await renderChatMessages();
            }
            startPolling();
        });

        document.getElementById('confirmDeleteChatButton').addEventListener('click', async function() {
    const userIdToArchive = this.getAttribute('data-user-id-to-delete');
    if (!userIdToArchive) return;

    try {
        // Step 1: Call the new backend API to archive it in the database
        const response = await fetch('../api/archive_conversation.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userIdToArchive })
        });

        const result = await response.json();

        if (result.status !== 'success') {
            throw new Error(result.message || 'Failed to archive on server.');
        }

        // Step 2: On success, immediately remove it from the UI for a fast response
        delete conversations[userIdToArchive];
        
        if (activeUserId == userIdToArchive) {
            const remainingIds = Object.keys(conversations);
            activeUserId = remainingIds.length > 0 ? remainingIds[0] : null;
            renderChatMessages(); // Update the message panel
        }
        
        renderConversationList(); // Redraw the conversation list

        bootstrap.Modal.getInstance(deleteChatModal).hide();

    } catch (error) {
        console.error('Error archiving conversation:', error);
        deleteChatModal.querySelector('#deleteChatModalBody').textContent = `Error: ${error.message}`;
    }
});

    </script>
</body>

</html>
