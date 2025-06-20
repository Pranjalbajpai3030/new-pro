<?php
// src/functions.php

function generateVerificationCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function saveVerificationCode($email, $code) {
    $filename = __DIR__ . '/temp_codes/' . md5(strtolower(trim($email))) . '.txt';
    file_put_contents($filename, $code);
}

function verifyCode($email, $code) {
    $filename = __DIR__ . '/temp_codes/' . md5(strtolower(trim($email))) . '.txt';
    if (!file_exists($filename)) return false;
    $storedCode = trim(file_get_contents($filename));
    return $storedCode === trim($code);
}

function deleteVerificationCode($email) {
    $filename = __DIR__ . '/temp_codes/' . md5(strtolower(trim($email))) . '.txt';
    if (file_exists($filename)) {
        unlink($filename);
    }
}

function cleanup_old_codes($dir, $expiryMinutes = 15) {
    $now = time();
    foreach (glob("$dir/*.txt") as $file) {
        if (is_file($file) && ($now - filemtime($file)) > ($expiryMinutes * 60)) {
            unlink($file);
        }
    }
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $email = strtolower(trim($email));

    if (!file_exists($file)) {
        file_put_contents($file, "");
    }

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $email = strtolower(trim($email));

    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_filter($emails, fn($e) => trim(strtolower($e)) !== $email);

    file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);
}

function sendVerificationEmail($email, $code, $isUnsubscribe = false) {
    $email = htmlspecialchars($email);
    $code = htmlspecialchars($code);
    
    $subject = $isUnsubscribe ? "Confirm Un-subscription" : "Your Verification Code";
    $message = $isUnsubscribe
        ? "<p>To confirm un-subscription, use this code: <strong>$code</strong></p>"
        : "<p>Your verification code is: <strong>$code</strong></p>";

    $message = "<html><body>$message</body></html>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    mail($email, $subject, $message, $headers);
}

function fetchAndFormatXKCDData() {
    $latest = json_decode(file_get_contents("https://xkcd.com/info.0.json"), true);
    $max = $latest['num'];
    $randId = random_int(1, $max);
    $data = json_decode(file_get_contents("https://xkcd.com/$randId/info.0.json"), true);

    $img = htmlspecialchars($data['img']);
    $title = htmlspecialchars($data['title']);
    $alt = htmlspecialchars($data['alt']);

    return "<h2>XKCD Comic: $title</h2>
            <img src=\"$img\" alt=\"$alt\" style=\"max-width:100%\">
            <p><a href=\"#\" id=\"unsubscribe-button\">Unsubscribe</a></p>";
}

function sendXKCDUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';

    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $xkcdContent = fetchAndFormatXKCDData();

    $subject = "Your XKCD Comic";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    foreach ($emails as $email) {
        $unsubscribeLink = "http://localhost:8000/unsubscribe.php?unsubscribe_email=" . urlencode($email);
        $personalizedContent = str_replace(
            '#',
            htmlspecialchars($unsubscribeLink),
            $xkcdContent
        );

        mail($email, $subject, $personalizedContent, $headers);
    }
}
