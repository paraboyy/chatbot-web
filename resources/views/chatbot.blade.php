<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Customer Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        #chatbox {
            width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        #messages {
            height: 300px;
            overflow-y: auto;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #fafafa;
        }
        #userInput {
            display: flex;
        }
        #userInput input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        #userInput button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div id="chatbox">
    <h3>Chatbot Customer Service</h3>
    <div id="messages"></div>

    <form id="userInput" action="javascript:void(0);">
        <input type="text" id="message" placeholder="Type your message..." required>
        <button type="submit">Send</button>
    </form>
</div>

<script>
    const form = document.getElementById('userInput');
    const messageInput = document.getElementById('message');
    const messagesDiv = document.getElementById('messages');

    // Function to display message in the chat
    function displayMessage(sender, text) {
        const messageDiv = document.createElement('div');
        messageDiv.textContent = `${sender}: ${text}`;
        messagesDiv.appendChild(messageDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight; // Auto-scroll to the latest message
    }

    // Check for CS response on page load
    document.addEventListener("DOMContentLoaded", function() {
        const csResponse = "{{ Session::get('cs_response') }}";
        if (csResponse) {
            displayMessage("CS", csResponse);
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const userMessage = messageInput.value;

        // Display user message
        displayMessage("You", userMessage);

        // Send message to the backend
        fetch('/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: userMessage })
        })
        .then(response => response.json())
        .then(data => {
            // Display bot response
            displayMessage("Bot", data.response);
        });

        messageInput.value = '';
    });
</script>

</body>
</html>
