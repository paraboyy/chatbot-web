<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Service</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 600px; margin: 0 auto; padding: 20px; }
        h1 { text-align: center; margin-bottom: 20px; }
        ul { list-style: none; padding: 0; }
        li { background-color: #f1f1f1; margin: 10px; padding: 10px; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); border: 2px solid #ccc;}
        .user-message { color: #007bff; }
        .cs-message { color: #dc3545; }
        .reply-form { margin-top: 10px; margin-left: 10px; }
        .reply-form input { width: calc(100% - 90px); padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 80%;}
        .reply-form button { padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .reply-form button:hover { background-color: #0056b3; }
        .delete-button { background-color: #dc3545; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        .delete-button:hover { background-color: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Customer Service</h1>
        <ul>
            @foreach($messages as $userUnix => $userMessages)
                <li>
                    <strong>User ID: {{ $userUnix }}</strong>
                    <ul>
                        @foreach($userMessages as $msg)
                            @if(trim($msg['message']) !== '') <!-- Memastikan pesan tidak kosong atau hanya spasi -->
                                <li class="{{ $msg['type'] == 'user' ? 'user-message' : 'cs-message' }}">
                                    <strong>{{ ucfirst($msg['type']) }}:</strong> {{ $msg['message'] }}
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    <!-- Form balasan CS -->
                    <form class="reply-form" onsubmit="replyToUser(event, '{{ $userUnix }}')">
                        <input type="text" placeholder="Balas pesan..." required>
                        <button type="submit">Kirim</button>
                    </form>
                    <!-- Tombol Hapus Chat -->
                    <button class="delete-button" onclick="deleteChat('{{ $userUnix }}')">Hapus Chat</button>
                </li>
            @endforeach
        </ul>
    </div>

    <script>
        function replyToUser(event, userUnix) {
            event.preventDefault();
            const messageInput = event.target.querySelector('input');
            const message = messageInput.value.trim(); // Mengambil dan memangkas input

            // Jangan kirim pesan kosong
            if (message === '') {
                return;
            }

            messageInput.value = ''; // Kosongkan input setelah submit

            fetch('/cs-reply', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ message, user_unix: userUnix })
            })
            .then(response => response.json())
            .then(data => {
                // Jika berhasil, tambahkan balasan baru ke UI
                const userMessagesList = event.target.previousElementSibling; // Dapatkan ul yang berisi pesan user
                const newMessage = document.createElement('li');
                newMessage.classList.add('cs-message');
                newMessage.innerHTML = `<strong>CS:</strong> ${message}`;
                userMessagesList.appendChild(newMessage); // Tambahkan pesan baru di akhir

                console.log('Pesan balasan:', data.messages);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Fungsi untuk menghapus chat
        function deleteChat(userUnix) {
            if (!confirm('Apakah Anda yakin ingin menghapus semua pesan dari user ini?')) return;

            fetch('/delete-chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ user_unix: userUnix })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hapus elemen li dari user yang sesuai
                    const userChat = document.querySelector(`li strong:contains(${userUnix})`).closest('li');
                    userChat.remove();
                    console.log('Chat berhasil dihapus');
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Fungsi untuk polling pesan dari user setiap 3 detik
        function pollMessages() {
            fetch('/cs')
            .then(response => response.json())
            .then(data => {
                displayMessages(data.messages);
            })
            .catch(error => {
                console.error('Error polling messages:', error);
            });
        }

        // Jalankan polling setiap 3 detik
        setInterval(pollMessages, 3000);

        // Muat pesan pertama kali saat halaman dimuat
        window.onload = pollMessages;
    </script>
</body>
</html>
