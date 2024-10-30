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
    <nav class="navbar bg-nav-home navbar-expand-lg">
        <div class="ms-4 my-2">
            <img src="/skote/images/logo-dark.png" alt="ISONER" height="24">
            <a class="text-dec fw-bold text-white" href="#">CHATBOT PENGADUAN</a>
        </div>
    </nav>

    <div class="dp-flex-center">
        <div id="messages" class="w-70"></div>
    </div>

    <div class="dp-flex-center">
        <div class="message-button">
            <input type="text" class="form-control" id="message" placeholder="Message Pengaduan" aria-label="Recipient's username" aria-describedby="button-addon2">
            <button onclick="sendMessage()">
                <span class="arrow">➔</span>
            </button>
        </div>

        <!-- <div class="input-group mb-3 w-70">
            <input type="text" class="form-control" id="message" placeholder="Message Pengaduan" aria-label="Recipient's username" aria-describedby="button-addon2">
            <button class="btn btn-outline-home" onclick="sendMessage()">Kirim</button>
        </div> -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

    <script> 

        //Menyimpan id chat
        const userUnix = @json($user_unix);
        localStorage.setItem('ID_Unix', userUnix);

         // Fungsi untuk menampilkan pesan di UI
         function displayMessage(message, type) {
            const messagesDiv = document.getElementById('messages');
            const messageDiv = document.createElement('div');


            messageDiv.className = 'message ' + type;
            
            // Tentukan ikon berdasarkan tipe pesan
            const icon = document.createElement('i');
            if (type === 'user') {
                icon.className = 'fas fa-user-circle me-2 text-primary fa-lg';
            } else if (type === 'cs') {
                icon.className = 'fas fa-headset me-2 text-success fa-lg';
            }

            // Tambahkan ikon dan teks pesan ke dalam elemen pesan
            messageDiv.appendChild(icon);
            const messageText = document.createElement('span');
            messageText.innerText = message;
            messageDiv.appendChild(messageText);

            messagesDiv.appendChild(messageDiv);

            // Scroll ke bawah setiap kali ada pesan baru
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        // Fungsi untuk mengirim pesan pengguna
        function sendMessage() {
            const messageInput = document.getElementById('message');
            const message = messageInput.value;
            messageInput.value = '';
            const service = [
                "1. Layanan Pengaduan Rumah Sakit Balimed",
                "2. Layanan Pengaduan Mahasiswa Udayana",
                "3. Layanan Pengaduan Pemprov Bali",
                "4. Layanan Pendaftaran UP mahasiswa TI",
            ];

            if (!message.trim()) {
                return; // Jangan kirim jika pesan kosong
            }

            // Tampilkan pesan pengguna
            displayMessage(message, 'user');

            // Tampilkan Balasan sesuai menu
            if (message === "1" || message === "1. Layanan Pengaduan Rumah Sakit Balimed") {
                setTimeout(() => {
                    const csResponse = "Anda telah diarahkan ke CS Layanan Pengaduan Rumah Sakit Balimed.";
                    displayMessage(csResponse, 'cs');
                }, 1000); 
            } else {
                setTimeout(() => {
                    const csReply = `Pilih Layanan:\n ${service.join('\n')}`;
                    displayMessage(csReply, 'cs');
                }, 1000); 
            }
        }   

        // Tampilkan pesan selamat datang saat halaman dimuat
        window.onload = function() {
            const welcomeMessage = "Selamat datang di Layanan Pengaduan ISONER, Apakah ada yang bisa kami bantu?";
            displayMessage(welcomeMessage, 'cs text-center fw-bold');
        }
    </script>
</body>
</html>