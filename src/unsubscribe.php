<?php
require_once 'functions.php';

$message = '';
cleanup_old_codes(__DIR__ . '/temp_codes', 15); // Expire old codes after 15 minutes

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['unsubscribe_email'] ?? ''));
    $code = trim($_POST['verification_code'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif (!isset($_POST['verification_code'])) {
        // Step 1: Send unsubscription verification code
        $generatedCode = generateVerificationCode();
        saveVerificationCode($email, $generatedCode);
        sendVerificationEmail($email, $generatedCode, true); // true = unsubscribe
        $message = "Unsubscribe verification code sent to $email.";
    } else {
        // Step 2: Verify unsubscription code
        if (!preg_match('/^\d{6}$/', $code)) {
            $message = "Verification code must be a 6-digit number.";
        } elseif (verifyCode($email, $code)) {
            unsubscribeEmail($email);
            deleteVerificationCode($email);
            $message = "You have been unsubscribed successfully.";
        } else {
            $message = "Invalid or expired verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unsubscribe</title>
</head>
<body>
    <h1>🔴 Unsubscribe from XKCD Comics</h1>
    <?php if ($message): ?>
        <p style="color: <?= strpos($message, 'success') !== false ? 'green' : 'red'; ?>;">
            <?= htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <!-- Unsubscribe Email Form -->
    <h2>Step 1: Request Code</h2>
    <form method="POST">
        <label>Enter your email to unsubscribe:</label><br>
        <input type="email" name="unsubscribe_email" required>
        <br><br>
        <button type="submit">Send Unsubscribe Code</button>
    </form>

    <br><hr><br>

    <!-- Verification Code Form -->
    <h2>Step 2: Confirm Code</h2>
    <form method="POST">
        <label>Email used for unsubscribe:</label><br>
        <input type="email" name="unsubscribe_email" required><br>
        <label>Enter verification code:</label><br>
        <input type="text" name="verification_code" maxlength="6" pattern="\d{6}" required><br><br>
        <button type="submit">Confirm Unsubscribe</button>
    </form>
</body>
</html>
