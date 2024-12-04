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
    <title>Pollinations Chat</title>
</head>
<body>
    <h1>Chat with Pollinations AI</h1>
    <div>
        <h2>Conversation</h2>
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; height: 300px; overflow-y: scroll;">
            <?php
            if (!empty($_SESSION['conversation'])) {
                foreach ($_SESSION['conversation'] as $message) {
                    echo nl2br(htmlspecialchars($message)) . "<br>";
                }
            }
            ?>
        </div>
    </div>
    <form method="post">
        <input type="text" name="user_input" placeholder="Type your message..." required>
        <button type="submit">Send</button>
    </form>
    <form method="post" action="reset.php" style="margin-top: 10px;">
        <button type="submit">Reset Conversation</button>
    </form>
</body>
</html>
