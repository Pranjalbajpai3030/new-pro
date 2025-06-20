<?php
require_once 'functions.php';

$statusMessage = '';
$codeExpiryMinutes = 15;

// Cleanup old codes (temp_codes/*.txt files older than 15 minutes)
cleanup_old_codes(__DIR__ . '/temp_codes', $codeExpiryMinutes);

// Handle subscription email submission
if (isset($_POST['send_code'])) {
    $email = strtolower(trim($_POST['email']));
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = generateVerificationCode();
        saveVerificationCode($email, $code);
        sendVerificationEmail($email, $code);
        $statusMessage = "Verification code sent to $email.";
    } else {
        $statusMessage = "Invalid email format.";
    }
}

// Handle verification of subscription code
if (isset($_POST['verify_code'])) {
    $email = strtolower(trim($_POST['verify_email']));
    $code = trim($_POST['verification_code']);
    if (!preg_match('/^\d{6}$/', $code)) {
        $statusMessage = "Verification code must be a 6-digit number.";
    } elseif (verifyCode($email, $code)) {
        registerEmail($email);
        deleteVerificationCode($email);
        $statusMessage = "Email successfully verified and subscribed.";
    } else {
        $statusMessage = "Invalid or expired verification code for subscription.";
    }
}

// Handle unsubscribe code request
if (isset($_POST['send_unsub_code'])) {
    $email = strtolower(trim($_POST['unsubscribe_email']));
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = generateVerificationCode();
        saveVerificationCode($email, $code);
        sendVerificationEmail($email, $code, true); // true = unsubscribe flag
        $statusMessage = "Unsubscribe verification code sent to $email.";
    } else {
        $statusMessage = "Invalid email format for unsubscription.";
    }
}

// Handle verification of unsubscribe code
if (isset($_POST['confirm_unsubscribe'])) {
    $email = strtolower(trim($_POST['unsubscribe_email_verify']));
    $code = trim($_POST['verification_code_unsub']);
    if (!preg_match('/^\d{6}$/', $code)) {
        $statusMessage = "Verification code must be a 6-digit number.";
    } elseif (verifyCode($email, $code)) {
        unsubscribeEmail($email);
        deleteVerificationCode($email);
        $statusMessage = "You have been unsubscribed successfully.";
    } else {
        $statusMessage = "Invalid or expired verification code for unsubscription.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>XKCD Email Subscription</title>
</head>
<body>
    <h1>📬 Subscribe / Unsubscribe to Daily XKCD Comics</h1>
    <?php if ($statusMessage): ?>
        <p><strong>Status:</strong> <?= htmlspecialchars($statusMessage); ?></p>
    <?php endif; ?>

    <h2>🟢 Subscribe</h2>
    <form method="POST" action="index.php">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <button type="submit" name="send_code">Send Verification Code</button>
    </form>

    <form method="POST" action="index.php">
        <label>Email used to receive code:</label><br>
        <input type="email" name="verify_email" required><br>
        <label>Enter Verification Code:</label><br>
        <input type="text" name="verification_code" pattern="\d{6}" required><br><br>
        <button type="submit" name="verify_code">Verify & Subscribe</button>
    </form>

    <hr>

    <h2>🔴 Unsubscribe</h2>
    <form method="POST" action="index.php">
        <label>Email:</label><br>
        <input type="email" name="unsubscribe_email" required><br><br>
        <button type="submit" name="send_unsub_code">Send Unsubscribe Code</button>
    </form>

    <form method="POST" action="index.php">
        <label>Email used to receive code:</label><br>
        <input type="email" name="unsubscribe_email_verify" required><br>
        <label>Verification Code:</label><br>
        <input type="text" name="verification_code_unsub" pattern="\d{6}" required><br><br>
        <button type="submit" name="confirm_unsubscribe">Confirm Unsubscribe</button>
    </form>
</body>
</html>
