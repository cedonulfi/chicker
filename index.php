<?php
session_start();

// Inisialisasi riwayat percakapan jika belum ada
if (!isset($_SESSION['conversation'])) {
    $_SESSION['conversation'] = [];
}

// API endpoint
$apiUrl = "https://text.pollinations.ai/";

// Ambil input user dari form (jika ada)
$userInput = isset($_POST['user_input']) ? trim($_POST['user_input']) : '';

if ($userInput) {
    // Tambahkan input user ke riwayat percakapan
    $_SESSION['conversation'][] = "User: $userInput";

    // Buat prompt dari riwayat percakapan
    $prompt = implode("\n", $_SESSION['conversation']) . "\nAssistant:";

    // URL-encode prompt
    $urlEncodedPrompt = urlencode($prompt);

    // Bentuk URL API
    $url = $apiUrl . $urlEncodedPrompt;

    // Kirim permintaan ke API
    $response = file_get_contents($url);

    // Ambil respons dari AI
    if ($response !== false) {
        $aiResponse = trim($response);

        // Tambahkan respons AI ke riwayat percakapan
        $_SESSION['conversation'][] = "Assistant: $aiResponse";
    } else {
        $aiResponse = "Sorry, I couldn't process your request.";
    }
} else {
    $aiResponse = "Start the conversation by typing something!";
}

// HTML untuk form input dan percakapan
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chicker - Chat with Pollinations AI</title>
    <style>
        /* Reset dasar */
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }

        /* Kontainer utama */
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        .conversation-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            height: 300px;
            overflow-y: auto;
            background: #fafafa;
        }

        .conversation-box p {
            margin: 5px 0;
            line-height: 1.5;
        }

        .user-message {
            color: #007BFF;
            font-weight: bold;
        }

        .assistant-message {
            color: #FF5722;
            font-weight: bold;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .reset-btn {
            background-color: #f44336;
        }

        .reset-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Chicker - Chat with Pollinations AI</h1>
        <div class="conversation-box">
            <?php
            if (!empty($_SESSION['conversation'])) {
                foreach ($_SESSION['conversation'] as $message) {
                    $messageType = strpos($message, 'User:') === 0 ? 'user-message' : 'assistant-message';
                    echo "<p class=\"$messageType\">" . nl2br(htmlspecialchars($message)) . "</p>";
                }
            } else {
                echo "<p>Start the conversation by typing something below!</p>";
            }
            ?>
        </div>
        <form method="post">
            <input type="text" name="user_input" placeholder="Type your message..." required>
            <button type="submit">Send</button>
        </form>
        <form method="post" action="reset.php" style="margin-top: 10px;">
            <button type="submit" class="reset-btn">Reset Conversation</button>
        </form>
    </div>
</body>
</html>
