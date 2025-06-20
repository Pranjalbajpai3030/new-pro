<?php
require_once 'functions.php';

$logFile = __DIR__ . '/cron_log.txt';

/**
 * Log a message with timestamp.
 */
function logMessage($message) {
    global $logFile;
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

/**
 * Rotate the log file if larger than 100KB
 */
if (file_exists($logFile) && filesize($logFile) > 102400) {
    $backupName = $logFile . '.' . time() . '.bak';
    rename($logFile, $backupName);
    file_put_contents($logFile, "[$backupName] rotated log\n");
}

try {
    // Send XKCD comic to all registered emails
    sendXKCDUpdatesToSubscribers();
    logMessage("✅ XKCD updates sent to subscribers.");

    // Clean old verification code files (older than 1 day)
    $tempDir = __DIR__ . '/temp_codes/';
    if (is_dir($tempDir)) {
        foreach (glob($tempDir . '*.txt') as $file) {
            if (filemtime($file) < time() - 86400) { // 24 hours
                unlink($file);
            }
        }
    }
    logMessage("🧹 Old temp code files cleaned up.");

} catch (Exception $e) {
    $error = "❌ Error in CRON job: " . $e->getMessage();
    logMessage($error);

    // Optional: Email alert to admin
    $adminEmail = "your-admin-email@example.com";  // Change this
    $subject = "XKCD CRON Job Failed";
    $body = "<p>$error</p><p>Check cron_log.txt for more details.</p>";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    mail($adminEmail, $subject, $body, $headers);
}
