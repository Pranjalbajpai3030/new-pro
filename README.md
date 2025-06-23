📬 XKCD Email Verification & Daily Comic Subscription System

This project is a PHP-based XKCD Email Verification and Subscription system built as part of an assignment. Users can subscribe via email, verify their email with a code, and receive a random XKCD comic every 24 hours via email. The system also supports secure unsubscription and logs everything for easy testing.

---

## 🚀 Project Overview

This PHP project allows users to:
- Register their email and receive a 6-digit verification code.
- Get subscribed to a daily XKCD comic feed.
- Unsubscribe via secure code-based email verification.
- Automatically receive daily emails via a CRON job.

A special `test.php` file is included to test and verify all functionality — without modifying any configuration files.

---

## 🛠️ Technologies Used

| Component            | Details                                 |
|----------------------|------------------------------------------|
| 🧠 Language           | PHP 8.3                                  |
| 📩 Email Function     | PHP's built-in `mail()` function ✅       |
| 📅 CRON Job           | Scheduled using a shell script (`setup_cron.sh`) |
| 📄 File Storage       | `registered_emails.txt` for subscriptions |
| 🧪 Test Coverage      | `test.php` verifies all functionality      |
| 📦 Temp Code Storage  | Stored using hashed `.txt` files in `temp_codes/` |

---

## 📧 Why Use `mail()`?

Per assignment requirements, **PHP's built-in `mail()` function** is used to send all emails:

- For verification codes
- For unsubscription confirmation
- For daily XKCD comic delivery

Using `mail()` keeps the system lightweight, dependency-free, and fully native. 

If `mail()` is **not configured** on the system (e.g., missing SMTP or sendmail), we **gracefully fallback** by logging email content to a file `src/emails.log` for testing.

✅ This ensures **zero configuration** for testers — the project just works.

---
---

## ⚙️ Configuration Required (If mail() is not working)

PHP's `mail()` function relies on an internal mail transfer agent (MTA). If your system doesn't have one, you'll need to configure `sendmail()` to ensure emails are actually delivered.

### 🛠️ How to Configure Sendmail (Linux/macOS)

Edit your `php.ini` file and add this line (or ensure it's not commented):

```ini
sendmail_path = /usr/sbin/sendmail -t -i
```

Make sure `sendmail` is installed:

```bash
sudo apt install sendmail
sudo service sendmail restart
```

### 🪟 For Windows

In your `php.ini`, update these lines:

```ini
SMTP = smtp.example.com
smtp_port = 25
sendmail_from = you@example.com
```

You’ll also need access to an SMTP server (e.g., Gmail, Mailtrap, etc.).

---

### 💡 Why We Use `sendmail()`

PHP's `mail()` function is simply a wrapper that hands the email data to your system's `sendmail` binary or SMTP configuration.

To ensure real emails are delivered:
- We rely on the default `sendmail_path` on Linux/macOS.
- We fallback to logging email to `emails.log` if `mail()` fails — this makes testing still possible without config.

---

## 🧩 Code Snippet: mail() + sendmail fallback (functions.php)

```php
function sendEmail($to, $subject, $message, $headers) {
    if (mail($to, $subject, $message, $headers)) {
        return true;
    }

    // Fallback: Save the email for testing if mail() fails
    $log = "TO: $to\nSUBJECT: $subject\nHEADERS: $headers\nMESSAGE:\n$message\n\n----------------------\n";
    file_put_contents(__DIR__ . '/emails.log', $log, FILE_APPEND);
    return false;
}
```

This ensures:
- ✅ If `sendmail` is correctly set up → mail is delivered
- ✅ If not → mail content is saved in `src/emails.log` for visibility

---
## ⚙️ Configuration Done

The following fallback mechanism is added to ensure testing works **even without email setup**:

- If `mail()` fails, the email is saved to:  
  📄 `src/emails.log`
- This makes the email visible without SMTP/sendmail.

📸 _**Screenshot of emails.log preview:**_  
![Screenshot 2025-06-23 133132](https://github.com/user-attachments/assets/9ae33c24-d0ea-4d6c-9aa5-ef4bf49e7cc9)
![Screenshot 2025-06-23 133006](https://github.com/user-attachments/assets/5d75936b-f213-46ea-8042-5ddedd5e52a3)


---

## ▶️ How to Run the Project

```bash
# 1. Move into the project directory
cd xkcd-email-system

# 2. Start local PHP server
php -S localhost:8000 -t src
```

Now open in your browser:

```
http://localhost:8000
```

You’ll see the email input form to subscribe, verify, and unsubscribe.

📸 _**Screenshot of OTP verification form:**_ 
![screencapture-localhost-8000-2025-06-23-13_36_27](https://github.com/user-attachments/assets/116fd74b-9a5b-4bc9-8594-0905a9e97746)
 ![Screenshot 2025-06-23 133219](https://github.com/user-attachments/assets/00d084d2-088d-43a3-8f5e-357ee051c2ba)
![Screenshot 2025-06-23 132949](https://github.com/user-attachments/assets/df4c65df-b031-4bc1-9ebb-77ab4a907122)

---

## 📆 How to Run CRON Job

```bash
# Setup CRON job to send XKCD comic daily at midnight
bash src/setup_cron.sh
```

You can also run it manually to test:

```bash
php src/cron.php
```

📸 _**Screenshot of comic email / logs:**_  
![Screenshot 2025-06-23 133006](https://github.com/user-attachments/assets/427acd90-d265-4dcc-aad9-8a1a1a062789)

---

## 🧪 Testing Everything Without Email Setup

The file `src/test.php` allows you to:

- Generate and verify OTP codes
- Register and unregister users
- Simulate sending verification & XKCD emails
- Show fallback email content (if `mail()` fails)

### ✅ To Run Tests:
```bash
php src/test.php
```

📄 Email contents will be printed or saved to:
```
src/emails.log
```

📸 _**Screenshot of test.php output:**_  
![Screenshot 2025-06-23 132914](https://github.com/user-attachments/assets/d5767c17-672c-4e82-a0ca-68549ca839db)


---

## ✅ Compliance with Assignment Rules

| Rule                                              | Status |
|---------------------------------------------------|--------|
| All code inside `src/`                            | ✅ Yes |
| Used `mail()` function only                       | ✅ Yes |
| No changes to `php.ini` or external dependencies  | ✅ Yes |
| CRON job for sending daily XKCD comic             | ✅ Yes |
| Unsubscribe via code-based email flow             | ✅ Yes |
| No hardcoded emails or databases used             | ✅ Yes |
| Logged or sent all emails in HTML format          | ✅ Yes |
| Fully testable via `test.php`                     | ✅ Yes |

---

## 📁 Project Structure

```
xkcd-email-system/
├── src/
│   ├── index.php               # Main frontend (subscribe/verify)
│   ├── unsubscribe.php         # Unsubscribe form
│   ├── functions.php           # Core logic
│   ├── cron.php                # Daily XKCD sender
│   ├── setup_cron.sh           # CRON registration
│   ├── test.php                # Full test runner
│   ├── registered_emails.txt   # Stores verified users
│   ├── emails.log              # Email fallback log
│   └── temp_codes/             # Temporary code storage
```

---

## 📌 Final Note

This project is designed to **work out of the box**, with:

- No external SMTP servers
- No database
- No config changes
- No dependency on any system mail agent

Whether you’re a tester or reviewer, just run the server — and it works.

---

Made with 💡 by Pranjal Bajpai
