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
    </style>
</head>
<body>
    <div class="chat-container">
        <center><h2>CHATBOT ISONER</h2></center>
        <div id="messages"></div>
        <input type="text" id="message" placeholder="Ketik pesan...">
        <button onclick="sendMessage()">Kirim</button>
    </div>

    <script>
        // Ambil user_unix dari localStorage jika ada
        let userUnix = localStorage.getItem('user_unix');
        let firstMessageSent = false; // Untuk menandai pesan pertama kali yang dikirim

        // Fungsi untuk menampilkan pesan di UI
        function displayMessages(messages) {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = ''; // Hapus semua pesan sebelum menampilkannya

            messages.forEach(msg => {
                // Cek apakah msg dan msg.message ada dan tidak kosong atau hanya terdiri dari spasi
                if (msg && msg.message && msg.message.trim() !== '') {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message ' + (msg.type === 'user' ? 'user' : 'cs');
                    messageDiv.innerText = msg.message;
                    messagesDiv.appendChild(messageDiv);
                }
            });
        }


        // Fungsi untuk menampilkan pesan selamat datang
        function welcomeMessage() {
            const messagesDiv = document.getElementById('messages');
            const welcomeDiv = document.createElement('div');
            welcomeDiv.className = 'message cs';
            welcomeDiv.innerText = "Selamat datang di website ISONER, ada yang bisa kami bantu.";
            messagesDiv.appendChild(welcomeDiv);
        }

        // Fungsi untuk mengirim pesan
        function sendMessage() {
            const messageInput = document.getElementById('message');
            const message = messageInput.value;
            messageInput.value = '';

            if (!firstMessageSent) {
                // Tampilkan pesan dari bot saat pesan pertama kali dikirim
                const botMessageDiv = document.createElement('div');
                botMessageDiv.className = 'message cs';
                botMessageDiv.innerText = "Pesan anda akan kami kirim kepada CS, harap menunggu.";
                document.getElementById('messages').appendChild(botMessageDiv);
                firstMessageSent = true; // Tandai bahwa pesan pertama sudah dikirim
            }

            // Tambahkan pesan pengguna
            const userMessageDiv = document.createElement('div');
            userMessageDiv.className = 'message user';
            userMessageDiv.innerText = message;
            document.getElementById('messages').appendChild(userMessageDiv);

            fetch('/send-message', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ message })
            })
            .then(response => response.json())
            .then(data => {
                // Simpan user_unix di localStorage
                userUnix = data.user_unix;
                localStorage.setItem('user_unix', userUnix);
                
                // Tampilkan pesan di UI
                displayMessages(data.messages);
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
                displayMessages(data.messages);
            });
        }

        // Ambil pesan sebelumnya ketika halaman dimuat ulang
        window.onload = function() {
            if (userUnix) {
                welcomeMessage(); // Tampilkan pesan selamat datang saat halaman dimuat
                receiveCSReply(); // Pastikan pesan user sebelumnya dimuat
            }
        }
    </script>
</body>
</html>
