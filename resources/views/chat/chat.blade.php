<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="shortcut icon" href='skote/images/favicon.ico'>
    <link rel="stylesheet" href="skote/css/style.css">
    <title>Chatbot Pengaduan</title>
</head>
<body>
    <nav class="navbar bg-nav-home navbar-expand-lg space-between">
        <div class="ms-4 my-2">
            <img src="/skote/images/logo-dark.png" alt="ISONER" height="24">
            <a class="text-dec fw-bold text-white" href="#">CHATBOT PENGADUAN</a>
        </div>
        <!-- Tombol hamburger untuk membuka/menutup sidebar -->
        <button class="btn btn-link text-white" id="toggle-sidebar" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i> <!-- Icon hamburger -->
        </button>
    </nav>

    <!-- Sidebar on the right -->
    <div class="sidebar shadow">
        <div class="new-chat" onclick="createNewChat()">Buat Chat Baru</div>
        <div id="chat-list">
            <!-- List of chats will be displayed here -->
        </div>
    </div>

    <!-- Main chat window -->
    <div class="dp-flex-center mt-3" style="margin-left: 260px;">
        <div id="messages" class="p-3 border rounded shadow"></div>
    </div>

    <div class="dp-flex-center mt-3">
        <div class="message-button shadow d-flex align-items-center w-70" style="margin-left: 260px;">
            <button class="dokum btn btn-secondary me-2" onclick="sendDocument()">
                <span class="fw-bold">@</span>
            </button>
            <input type="text" class="form-control" id="message" placeholder="Message Pengaduan" aria-label="Recipient's username">
            <button class="btn btn-primary ms-2" onclick="sendMessage()">Kirim</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

    <script>
        const userUnix = @json($user_unix);
        localStorage.setItem('ID_Unix', userUnix);
        let chatHistory = JSON.parse(localStorage.getItem('chatHistory')) || []; // Load from localStorage
        let currentChatId = null; // Track current chat

        // Display the messages in the selected chat
        function displayMessage(message, type) {
            const messagesDiv = document.getElementById('messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message ' + type + ' p-2 rounded mb-2';

            const icon = document.createElement('i');
            if (type === 'user') {
                icon.className = 'fas fa-user-circle me-2 text-primary fa-lg';
                messageDiv.style.textAlign = 'right';
            } else if (type === 'cs') {
                icon.className = 'fas fa-headset me-2 text-success fa-lg';
            }

            messageDiv.appendChild(icon);
            const messageText = document.createElement('span');
            messageText.innerText = message;
            messageDiv.appendChild(messageText);

            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        // Send message to the chatbot
        async function sendMessage() {
            const messageInput = document.getElementById('message');
            const message = messageInput.value.trim();
            messageInput.value = '';

            if (!message) return;

            displayMessage(message, 'user');
            chatHistory[currentChatId].messages.push({ text: message, type: 'user' });
            saveChatHistory();
            console.log(chatHistory);
            console.log(message);

            try {
                const response = await fetch("{{ route('chat.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ message })
                });

                const result = await response.json();
                if (result.status === 'Message saved successfully') {
                    displayCSResponse(message);
                } else {
                    console.error("Failed to save message");
                }
            } catch (error) {
                console.error("Error sending message:", error);
            }
        }

        // Handle CS response
        function displayCSResponse(message) {
            const services = [
                "1. Layanan Pengaduan Rumah Sakit Balimed",
                "2. Layanan Pengaduan Mahasiswa Udayana",
                "3. Layanan Pengaduan Pemprov Bali",
                "4. Layanan Pendaftaran UP mahasiswa TI",
            ];

            setTimeout(() => {
                let response = "Pilih Layanan:\n" + services.join('\n');

                if (message === "1" || message === "1. Layanan Pengaduan Rumah Sakit Balimed") {
                    response = "Anda telah diarahkan ke CS Layanan Pengaduan Rumah Sakit Balimed.";
                } else if (message === "2" || message === "2. Layanan Pengaduan Mahasiswa Udayana") {
                    response = "Anda telah diarahkan ke CS Layanan Pengaduan Mahasiswa Udayana.";
                } else if (message === "3" || message === "3. Layanan Pengaduan Pemprov Bali") {
                    response = "Anda telah diarahkan ke CS Layanan Pengaduan Pemprov Bali.";
                } else if (message === "4" || message === "4. Layanan Pendaftaran UP mahasiswa TI") {
                    response = "Anda telah diarahkan ke CS Layanan Pendaftaran UP mahasiswa TI.";
                }

                chatHistory[currentChatId].messages.push({ text: response, type: 'cs' });
                saveChatHistory();
                displayMessage(response, 'cs');
            }, 1000);
        }

        // Create new chat
        function createNewChat() {
            const newChat = {
                id: Date.now(),
                name: `Chat ${chatHistory.length + 1}`,
                messages: []
            };
            chatHistory.push(newChat); // Add to history
            currentChatId = chatHistory.length - 1; // Set the current chat
            saveChatHistory(); // Save to localStorage
            updateChatList(); // Update the sidebar
            loadChat(currentChatId); // Load the new chat window
        }

        // Save chat history to localStorage
        function saveChatHistory() {
            localStorage.setItem('chatHistory', JSON.stringify(chatHistory));
        }

        // Load a specific chat
        function loadChat(chatId) {
            currentChatId = chatId;
            const chat = chatHistory[chatId];

            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = ''; // Clear current messages

            chat.messages.forEach(msg => {
                displayMessage(msg.text, msg.type);
            });

            // Highlight active chat
            updateChatList();
        }

        // Update chat list in the sidebar
        function updateChatList() {
            const chatListDiv = document.getElementById('chat-list');
            chatListDiv.innerHTML = ''; // Clear current list

            chatHistory.forEach((chat, index) => {
                const chatContainer = document.createElement('div');
                chatContainer.classList.add('d-flex', 'justify-content-between', 'align-items-center', 'chat-item-container');

                const chatLink = document.createElement('a');
                chatLink.href = '#';
                chatLink.innerText = chat.name || `Chat ${index + 1}`;  // Default name if not set
                chatLink.classList.add('chat-item');
                if (index === currentChatId) {
                    chatLink.classList.add('active-chat');
                }

                chatLink.onclick = () => loadChat(index);
                chatContainer.appendChild(chatLink);

                // Create the dropdown menu for edit and delete options
                const optionsMenu = document.createElement('div');
                optionsMenu.classList.add('dropdown');
                const optionsButton = document.createElement('button');
                optionsButton.classList.add('btn', 'btn-sm', 'btn-link');
                optionsButton.setAttribute('type', 'button');
                optionsButton.setAttribute('data-bs-toggle', 'dropdown');
                optionsButton.innerHTML = '<i class="fas fa-ellipsis-v"></i>';
                optionsMenu.appendChild(optionsButton);

                const dropdownMenu = document.createElement('ul');
                dropdownMenu.classList.add('dropdown-menu');
                dropdownMenu.innerHTML = `
                    <li><a class="dropdown-item" href="#" onclick="editChatName(${index})">Edit</a></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteChat(${index})">Delete</a></li>
                `;
                optionsMenu.appendChild(dropdownMenu);

                chatContainer.appendChild(optionsMenu);
                chatListDiv.appendChild(chatContainer);
            });
        }

        // Edit chat name
        function editChatName(chatId) {
            const newName = prompt('Enter new chat name:', chatHistory[chatId].name);
            if (newName) {
                chatHistory[chatId].name = newName;
                saveChatHistory();
                updateChatList();
            }
        }

        // Function to delete chat
        function deleteChat(chatId) {
            if (confirm("Are you sure you want to delete this chat?")) {
                chatHistory.splice(chatId, 1); // Remove the chat from the history
                saveChatHistory(); // Save updated chat history
                if (currentChatId === chatId) {
                    currentChatId = null; // Reset current chat if the deleted one was active
                    document.getElementById('messages').innerHTML = ''; // Clear messages
                }
                updateChatList(); // Update the chat list in the sidebar
            }
        }

        // Initialize the page
        window.onload = function() {
            if (chatHistory.length > 0) {
                loadChat(chatHistory.length - 1); // Load the last chat if any
            }

            updateChatList(); // Update the sidebar chat list
        };

        // Handle document upload (not fully implemented)
        function sendDocument() {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.onchange = async () => {
                const file = fileInput.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append("document", file);
                formData.append("_token", "{{ csrf_token() }}");

                try {
                    const response = await fetch("/upload-document", {
                        method: "POST",
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        displayMessage("Dokumen berhasil diunggah!", "cs");
                    } else {
                        displayMessage("Gagal mengunggah dokumen.", "cs");
                    }
                } catch (error) {
                    displayMessage("Terjadi kesalahan saat mengunggah dokumen.", "cs");
                }
            };
            fileInput.click();
        };

        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('open'); // Menambahkan/menyembunyikan class "open"
        }
    </script>
</body>
</html>
