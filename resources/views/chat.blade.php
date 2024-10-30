<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .chat-container { width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .message { margin: 10px; padding: 10px; border-radius: 5px; }
        .user { background-color: #d1e7dd; text-align: right; }
        .cs { background-color: #f8d7da; text-align: left; }
        #message { width: calc(100% - 90px); padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px; border: none; background-color: #007bff; color: white; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        #messages { height: 300px; overflow-y: auto; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="chat-container">
        <center><h2>CHATBOT ISONER</h2></center>
        <div id="messages"></div>
        <input type="text" id="message" placeholder="Ketik pesan...">
        <button onclick="sendMessage()">Kirim</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        let userUnix = localStorage.getItem('user_unix');

        // Fungsi untuk menampilkan pesan di UI
        function displayMessages(messages) {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = ''; // Hapus semua pesan sebelum menampilkannya

            messages.forEach(msg => {
                if (msg && msg.message && msg.message.trim() !== '') {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message ' + (msg.type === 'user' ? 'user' : 'cs');
                    messageDiv.innerText = msg.message;
                    messagesDiv.appendChild(messageDiv);
                }
            });

            // Scroll ke bawah setiap kali ada pesan baru
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        // Fungsi untuk mengirim pesan
        function sendMessage() {
            const messageInput = document.getElementById('message');
            const message = messageInput.value;
            messageInput.value = '';

            if (!message.trim()) {
                return; // Jangan kirim jika pesan kosong
            }

            const userMessage = {
                type: 'user',
                message: message,
                date: new Date().toISOString()
            };

            // Kirim pesan ke server
            fetch('/send-message', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ message })
            })
            .then(response => response.json())
            .then(data => {
                userUnix = data.user_unix;
                localStorage.setItem('user_unix', userUnix);

                // Tampilkan pesan dari server jika ada
                if (data.messages && data.messages.length) {
                    displayMessages(data.messages);
                }
            });
        }

        // Fungsi untuk menerima balasan dari CS
        function receiveCSReply() {
            fetch('/cs-reply', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ user_unix: userUnix })
            })
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length) {
                    displayMessages(data.messages);
                }
            });
        }

        // Polling balasan dari CS setiap 3 detik
        setInterval(function() {
            if (userUnix) {
                receiveCSReply();
            }
        }, 3000);

        // Ambil pesan sebelumnya ketika halaman dimuat
        window.onload = function() {
            if (userUnix) {
                fetch('/load-messages', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ user_unix: userUnix })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.messages) {
                        displayMessages(data.messages);
                    }
                });
            } else {
                welcomeMessage(); // Tampilkan pesan selamat datang saat pertama kali pengguna mengakses halaman
            }
        }
        
        // Fungsi untuk menampilkan pesan selamat datang
        function welcomeMessage() {
            const messagesDiv = document.getElementById('messages');
            const welcomeDiv = document.createElement('div');
            welcomeDiv.className = 'message cs';
            welcomeDiv.innerText = "Selamat datang di website ISONER, ada yang bisa kami bantu.";
            messagesDiv.appendChild(welcomeDiv);
        }
    </script>
</body>
</html>
