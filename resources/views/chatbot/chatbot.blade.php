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
    <div class="sidebar shadow" id="sidebar">
        <div class="ms-4 my-2 dp-flex mt-4">
            <img src="/skote/images/logo-light.svg" alt="ISONER" height="24" class="mx-1">
            <p class="text-decoration-none fw-bold d-block" href="#">CHATBOT PENGADUAN</p>
        </div>
        <div class="dp-flex-center">
            <button class="new-chat w-70 rounded shadow p-2 text-center" onclick="createNewChat()">Buat Chat Baru</button>
        </div>
        <div id="chat-list" class="mt-3 p-3">
            <!-- List of chats will be displayed here -->
            <p class="text-muted">Daftar chat akan muncul di sini.</p>
        </div>
    </div>

    <div class="content" id="content">
        <nav class="navbar">
            <button class="btn btn-link text-gray" id="toggle-sidebar" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <a class="p-2 fw-bold">CHATBOT PENGADUAN</a>
        </nav>

        <div class="text-center p-3">
            <h2 class="fw-bold">Pilih Chatbot</h2>
            <p>Text deskripsi</p>
        </div>

        <div class="search-container dp-flex-center p-5">
            <input type="text" id="searchInput" class="search-input" placeholder="Cari Chatbot...">
            <button class="search-button btn-submit" onclick="search()">Search</button>
        </div>

        <!-- Cards for chatbot list -->
        <div class="container">
            <div class="row" id="chatbot-list">
                <!-- Chatbot cards will be dynamically inserted here -->
            </div>
        </div>
    </div>

    <script>
        // Data dummy chatbot
        const chatbots = [
            {
                id: 1,
                image: "https://img.freepik.com/premium-vector/chatbot-icon-concept-chat-bot-chatterbot-robot-virtual-assistance-website_123447-1615.jpg?w=1380",
                name: "Chatbot 1",
                description: "Ini adalah chatbot pertama untuk layanan pengaduan."
            },
            {
                id: 2,
                image: "https://img.freepik.com/premium-vector/chatbot-icon-concept-chat-bot-chatterbot-robot-virtual-assistance-website_123447-1615.jpg?w=1380",
                name: "Chatbot 2",
                description: "Chatbot kedua dirancang untuk membantu pengguna lebih cepat."
            },
            {
                id: 3,
                image: "https://img.freepik.com/premium-vector/chatbot-icon-concept-chat-bot-chatterbot-robot-virtual-assistance-website_123447-1615.jpg?w=1380",
                name: "Chatbot 3",
                description: "Chatbot ini memiliki fitur AI yang lebih canggih."
            }
        ];

        let chatHistory = JSON.parse(localStorage.getItem("chatHistory")) || [];
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');

            sidebar.classList.toggle('closed');
            content.classList.toggle('full-width');
        }

        // Function to render chatbot cards
        function renderChatbotList() {
            const chatbotListContainer = document.getElementById('chatbot-list');
            chatbotListContainer.innerHTML = ""; 
            chatbots.forEach(chatbot => {
                const card = `
                    <div class="col-sm-6 mb-3 mb-sm-0 p-1">
                        <div class="flex-row card shadow" onclick="selectChatbot(${chatbot.id})" style="cursor: pointer;">
                            <img src="${chatbot.image}" class="card-img-top w-25" alt="${chatbot.name}">
                            <div class="card-body">
                                <h5 class="card-title">${chatbot.name}</h5>
                                <p class="card-text">${chatbot.description}</p>
                            </div>
                        </div>
                    </div>
                `;
                chatbotListContainer.innerHTML += card;
            });
        }

        // Function to select chatbot
        function selectChatbot(chatbotId) {
            const selectedChatbot = chatbots.find(c => c.id === chatbotId);
            if (!selectedChatbot) return;

            // Save to localStorage
            const newChat = {
                id: Date.now(),
                name: selectedChatbot.name,
                chatbotId: selectedChatbot.id,
                messages: []
            };

            chatHistory.push(newChat);
            localStorage.setItem("chatHistory", JSON.stringify(chatHistory));

            // Update sidebar and show message
            updateChatList();
            Swal.fire({
                icon: 'success',
                title: 'Membuat Chat Baru',
                text: `Chat telah dibuat di sidebar.`,
                confirmButtonText: 'OK'
            });
        }

        // Update sidebar chat list
        function updateChatList() {
            const chatListDiv = document.getElementById('chat-list');
            chatListDiv.innerHTML = ''; // Clear current list

            const sortedChats = chatHistory.sort((a, b) => b.id - a.id);

            chatHistory.forEach((chat, index) => {
                const chatContainer = document.createElement('div');
                chatContainer.classList.add('d-flex', 'justify-content-between', 'align-items-center', 'chat-item-container');

                const chatLink = document.createElement('a');
                chatLink.href = '/';
                chatLink.innerText = chat.name || `Chat ${index + 1}`;  // Default name if not set
                chatLink.classList.add('chat-item', 'text-gray');

                // chatLink.onclick = () => loadChat(index);
                chatContainer.appendChild(chatLink);

                // Create the dropdown menu for edit and delete options
                const optionsMenu = document.createElement('div');
                optionsMenu.classList.add('dropdown');
                const optionsButton = document.createElement('button');
                optionsButton.classList.add('btn', 'btn-sm', 'btn-link', 'text-light');
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

        // Toggle sidebar visibility
        // function toggleSidebar() {
        //     const sidebar = document.querySelector('.sidebar');
        //     sidebar.classList.toggle('open');
        // }

        // On page load
        document.addEventListener("DOMContentLoaded", () => {
            renderChatbotList(); // Render chatbot cards
            updateChatList();    // Render chat list in sidebar
        });

        // Function to edit chat name using SweetAlert
        function editChatName(chatId) {
            const currentName = chatHistory[chatId].name;

            Swal.fire({
                title: 'Edit Nama Chat',
                input: 'text',
                inputValue: currentName,
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Nama chat tidak boleh kosong!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const newName = result.value; // Get the new name from the input
                    if (newName !== currentName) {
                        chatHistory[chatId].name = newName;
                        saveChatHistory();
                        updateChatList(); // Update the chat list after the name is changed

                        // Show success alert
                        Swal.fire({
                            icon: 'success',
                            title: 'Nama Chat Diperbarui',
                            text: `Nama chat telah diubah menjadi ${newName}.`,
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        }


        function saveChatHistory() {
            localStorage.setItem('chatHistory', JSON.stringify(chatHistory));
        }

        // Function to delete chat
        function deleteChat(chatId) {
            Swal.fire({
                title: 'Hapus Chat?',
                text: "Chat ini akan dihapus dan tidak dapat dipulihkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus Chat',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    chatHistory.splice(chatId, 1);
                    saveChatHistory();
                    updateChatList(); // Update the chat list in the sidebar

                    Swal.fire(
                        'Deleted!',
                        'Chat telah dihapus.',
                        'success'
                    );
                }
            });
        }

        // Load a specific chat
        // function loadChat(chatId) {
        //     currentChatId = chatId;
        //     const chat = chatHistory[chatId];

        //     const messagesDiv = document.getElementById('messages');
        //     messagesDiv.innerHTML = ''; // Clear current messages

        //     chat.messages.forEach(msg => {
        //         displayMessage(msg.text, msg.type);
        //     });

        //     // Highlight active chat
        //     updateChatList();
        // }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
