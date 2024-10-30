<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="shortcut icon" href='skote/images/favicon.ico'>
    <link rel="stylesheet" href="skote/css/style.css">
    <title>Chatbot Pengaduan</title>
</head>
<body>
    <nav class="navbar bg-nav-home navbar-expand-lg">
        <div class="ms-4 my-2">
            <img src="/skote/images/logo-dark.png" alt="ISONER" height="24">
            <a class="text-dec fw-bold text-white" href="#">CHATBOT PENGADUAN</a>
        </div>
    </nav>

    <div id="messages"></div>

    <div class="dp-flex-center">
        <div class="input-group mb-3 w-70">
            <input type="text" class="form-control" id="message" placeholder="Message Pengaduan" aria-label="Recipient's username" aria-describedby="button-addon2">
            <button class="btn btn-outline-home" onclick="sendMessage()">Kirim</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

    <script> 
         // Fungsi untuk menampilkan pesan di UI
         function displayMessage(message, type) {
            const messagesDiv = document.getElementById('messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message ' + type;
            messageDiv.innerText = message;
            messagesDiv.appendChild(messageDiv);

            // Scroll ke bawah setiap kali ada pesan baru
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        // Fungsi untuk mengirim pesan pengguna
        function sendMessage() {
            const messageInput = document.getElementById('message');
            const message = messageInput.value;
            messageInput.value = '';

            if (!message.trim()) {
                return; // Jangan kirim jika pesan kosong
            }

            // Tampilkan pesan pengguna
            displayMessage(message, 'user');

            // Simulasi balasan dari CS
            setTimeout(() => {
                const csReply = "CS: Terima kasih atas pesan Anda. Kami akan segera merespon.";
                displayMessage(csReply, 'cs');
            }, 3000); // Balasan otomatis setelah 1 detik
        }   

        // Tampilkan pesan selamat datang saat halaman dimuat
        window.onload = function() {
            const welcomeMessage = "Selamat datang di Layanan Pengaduan ISONER, Apakah ada yang bisa kami bantu?";
            displayMessage(welcomeMessage, 'cs text-center');
        }
    </script>
</body>
</html>