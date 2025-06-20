<?php
require_once 'functions.php';

$email = "testuser@example.com";

// Test: Code Generation
$code = generateVerificationCode();
echo "Generated Code: $code\n";

// Test: Save Code
saveVerificationCode($email, $code);
echo "Code saved to temp_codes.\n";

// Test: Verify Code
$isVerified = verifyCode($email, $code) ? "✅ Pass" : "❌ Fail";
echo "Code Verification: $isVerified\n";

// Test: Delete Code
deleteVerificationCode($email);
$deleted = !file_exists(__DIR__ . "/temp_codes/" . md5($email) . ".txt") ? "✅ Deleted" : "❌ Still Exists";
echo "Code Deletion: $deleted\n";

// Test: Register Email
registerEmail($email);
echo "Email registered: $email\n";

// Test: Unsubscribe Email
unsubscribeEmail($email);
echo "Email unsubscribed: $email\n";

// Test: Cleanup Old Codes
echo "Creating old code for cleanup...\n";
$oldFile = __DIR__ . '/temp_codes/' . md5("olduser@example.com") . '.txt';
file_put_contents($oldFile, "123456");
touch($oldFile, time() - (16 * 60)); // set file time to 16 minutes ago
cleanup_old_codes(__DIR__ . '/temp_codes', 15);
$exists = file_exists($oldFile) ? "❌ Not Cleaned" : "✅ Cleaned";
echo "Old Code Cleanup: $exists\n";

// Test: Fetch XKCD Comic
$html = fetchAndFormatXKCDData();
echo "\nFetched XKCD HTML:\n" . (strlen($html) > 50 ? "✅ Fetched" : "❌ Fail") . "\n";

// Test: Send Email (commented out by default)
/*
sendVerificationEmail($email, $code);
echo "Email sent to $email\n";
*/

// Test: CRON Comic Sending (dry run)
echo "\nSimulating XKCD Comic Send...\n";
sendXKCDUpdatesToSubscribers();
echo "✅ Comics sent to registered users (if any).\n";
?>
