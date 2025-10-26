<?php
// admin.php - Admin Panel for CryptoCove
session_start();

// Simple authentication (in production, use proper authentication)
$admin_username = 'admin';
$admin_password = 'admin123';

// Email Configuration
define('ADMIN_EMAIL', 'aa9769850@gmail.com');
define('SITE_NAME', 'CryptoCove');

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    // Check login attempt
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($username === $admin_username && $password === $admin_password) {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $error = "Invalid credentials!";
        }
    }
    
    // Show login form if not logged in
    if (!isset($_SESSION['admin_logged_in'])) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin Login - CryptoCove</title>
            <style>
                :root {
                    --primary: #008751;
                    --primary-dark: #006441;
                    --background: #0d1117;
                    --surface: #161b22;
                    --text: #ffffff;
                    --text-light: #8b949e;
                    --border: #30363d;
                    --error: #ff6b6b;
                    --radius: 12px;
                    --shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
                    --glow: 0 0 20px rgba(0, 135, 81, 0.3);
                }
                
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                    font-family: 'Segoe UI', sans-serif;
                }
                
                body {
                    background: var(--background);
                    color: var(--text);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    background-image: 
                        radial-gradient(circle at 10% 20%, rgba(0, 135, 81, 0.1) 0%, transparent 20%),
                        radial-gradient(circle at 90% 80%, rgba(0, 135, 81, 0.1) 0%, transparent 20%);
                }
                
                .login-container {
                    background: var(--surface);
                    border-radius: var(--radius);
                    padding: 40px;
                    box-shadow: var(--shadow);
                    border: 1px solid var(--border);
                    width: 100%;
                    max-width: 400px;
                    position: relative;
                }
                
                .login-container::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 4px;
                    background: linear-gradient(90deg, var(--primary), #ffffff);
                    border-radius: var(--radius) var(--radius) 0 0;
                }
                
                .login-header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                
                .logo {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                    font-size: 1.8rem;
                    font-weight: 700;
                    background: linear-gradient(135deg, var(--primary) 0%, #ffffff 100%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    margin-bottom: 10px;
                }
                
                .login-title {
                    font-size: 1.5rem;
                    margin-bottom: 5px;
                }
                
                .login-subtitle {
                    color: var(--text-light);
                    font-size: 0.9rem;
                }
                
                .form-group {
                    margin-bottom: 20px;
                }
                
                .form-label {
                    display: block;
                    margin-bottom: 8px;
                    font-weight: 500;
                }
                
                .form-control {
                    width: 100%;
                    padding: 12px 16px;
                    background: rgba(13, 17, 23, 0.5);
                    border: 1px solid var(--border);
                    border-radius: var(--radius);
                    color: var(--text);
                    font-size: 1rem;
                    transition: all 0.3s ease;
                }
                
                .form-control:focus {
                    outline: none;
                    border-color: var(--primary);
                    box-shadow: 0 0 0 3px rgba(0, 135, 81, 0.1);
                }
                
                .btn {
                    width: 100%;
                    padding: 14px;
                    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                    color: white;
                    border: none;
                    border-radius: var(--radius);
                    font-size: 1rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    box-shadow: var(--glow);
                }
                
                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 10px 25px rgba(0, 135, 81, 0.4);
                }
                
                .error {
                    background: rgba(255, 107, 107, 0.1);
                    border: 1px solid rgba(255, 107, 107, 0.3);
                    color: var(--error);
                    padding: 12px;
                    border-radius: var(--radius);
                    margin-bottom: 20px;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="login-container">
                <div class="login-header">
                    <div class="logo">
                        <span>🇳🇬</span>
                        <span>CryptoCove</span>
                    </div>
                    <h2 class="login-title">Admin Panel</h2>
                    <p class="login-subtitle">Secure access to exchange management</p>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="login" value="1">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn">Login to Admin Panel</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Load data files
$data_file = 'exchange_data.json';
$crypto_file = 'cryptocurrencies.json';

if (!file_exists($data_file) || !file_exists($crypto_file)) {
    die("Data files not found. Please make sure the exchange platform is set up correctly.");
}

$data = json_decode(file_get_contents($data_file), true);
$crypto_data = json_decode(file_get_contents($crypto_file), true);

// Enhanced Telegram notification function for admin
function sendTelegramNotification($message) {
    $bot_token = '8439229450:AAFg8z-Ijca7Y2LWyI5-Z87Rv8ZVxs3AJ5Q';
    $chat_id = '8330683037';
    
    // Remove the blocking condition
    if (empty($bot_token)) {
        error_log("Telegram bot token not configured");
        return false;
    }
    
    $url = "https://api.telegram.org/bot" . $bot_token . "/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
            'timeout' => 10
        ]
    ];
    
    try {
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        
        if ($result === false) {
            error_log("Telegram notification failed to send from admin panel");
            return false;
        }
        
        $response = json_decode($result, true);
        if (!$response['ok']) {
            error_log("Telegram API error: " . ($response['description'] ?? 'Unknown error'));
            return false;
        }
        
        error_log("Admin Telegram notification sent successfully!");
        return true;
    } catch (Exception $e) {
        error_log("Admin Telegram notification error: " . $e->getMessage());
        return false;
    }
}

// Enhanced Email notification function for admin
function sendEmailNotification($subject, $message, $to = null) {
    if ($to === null) {
        $to = ADMIN_EMAIL;
    }
    
    try {
        $headers = "From: " . SITE_NAME . " Admin <admin@" . $_SERVER['HTTP_HOST'] . ">\r\n";
        $headers .= "Reply-To: admin@" . $_SERVER['HTTP_HOST'] . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "X-Priority: 1 (Highest)\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        $html_message = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>$subject</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Arial, sans-serif; 
                    background: linear-gradient(135deg, #0d1117 0%, #161b22 100%);
                    margin: 0;
                    padding: 20px;
                    color: #ffffff;
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    background: #161b22; 
                    border-radius: 12px; 
                    overflow: hidden;
                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
                    border: 1px solid #30363d;
                }
                .header { 
                    background: linear-gradient(135deg, #008751 0%, #00a862 100%); 
                    color: white; 
                    padding: 30px; 
                    text-align: center;
                    border-bottom: 4px solid #006441;
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 28px;
                    font-weight: 700;
                }
                .content { 
                    padding: 30px; 
                    line-height: 1.6;
                    background: #161b22;
                }
                .notification-box {
                    background: rgba(0, 135, 81, 0.1);
                    border: 1px solid rgba(0, 135, 81, 0.3);
                    border-radius: 8px;
                    padding: 20px;
                    margin: 20px 0;
                }
                .footer { 
                    background: #0d1117; 
                    padding: 20px; 
                    text-align: center; 
                    color: #8b949e;
                    font-size: 12px;
                    border-top: 1px solid #30363d;
                }
                .info-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 10px;
                    padding: 8px 0;
                    border-bottom: 1px solid #30363d;
                }
                .info-label {
                    font-weight: 600;
                    color: #8b949e;
                }
                .info-value {
                    font-weight: 600;
                    color: #ffffff;
                }
                .highlight {
                    background: linear-gradient(135deg, #008751 0%, #00a862 100%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    font-weight: 700;
                }
                .status-update {
                    background: rgba(255, 209, 102, 0.1);
                    border: 1px solid rgba(255, 209, 102, 0.3);
                    border-radius: 8px;
                    padding: 15px;
                    margin: 15px 0;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>⚡ " . SITE_NAME . " Admin</h1>
                    <p>Administrative Notification</p>
                </div>
                <div class='content'>
                    <div class='notification-box'>
                        " . nl2br(htmlspecialchars($message)) . "
                    </div>
                    <div class='status-update'>
                        <p><strong>Action Required:</strong> This action was performed through the admin panel.</p>
                        <p>Please verify the changes in your admin dashboard.</p>
                    </div>
                </div>
                <div class='footer'>
                    <p>This is an administrative notification from " . SITE_NAME . " Exchange Platform.</p>
                    <p>© " . date('Y') . " " . SITE_NAME . ". All rights reserved.</p>
                    <p>If you didn't perform this action, please secure your account immediately.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $success = mail($to, $subject, $html_message, $headers);
        
        if (!$success) {
            error_log("Admin email notification failed to send to: $to");
            return false;
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Admin email notification error: " . $e->getMessage());
        return false;
    }
}

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $request_id = $_POST['request_id'];
        $new_status = $_POST['status'];
        $admin_notes = $_POST['admin_notes'] ?? '';
        
        if (isset($data['exchange_requests'][$request_id])) {
            $old_status = $data['exchange_requests'][$request_id]['status'];
            $data['exchange_requests'][$request_id]['status'] = $new_status;
            $data['exchange_requests'][$request_id]['admin_notes'] = $admin_notes;
            $data['exchange_requests'][$request_id]['processed_at'] = date('Y-m-d H:i:s');
            
            // Save updated data
            file_put_contents($data_file, json_encode($data));
            
            $request = $data['exchange_requests'][$request_id];
            
            // Send Enhanced Telegram notification for status update
            $telegram_message = "⚡ <b>EXCHANGE REQUEST UPDATED</b>\n\n";
            $telegram_message .= "📋 <b>Request ID:</b> <code>$request_id</code>\n";
            $telegram_message .= "🔄 <b>Status Changed:</b> $old_status → $new_status\n";
            $telegram_message .= "👤 <b>Customer:</b> {$request['name']}\n";
            $telegram_message .= "📞 <b>Phone:</b> {$request['phone']}\n";
            $telegram_message .= "💰 <b>Exchange:</b> {$request['amount']} {$request['crypto']} → ₦" . number_format($request['naira_amount'], 2) . "\n";
            $telegram_message .= "📝 <b>Admin Notes:</b> $admin_notes\n";
            $telegram_message .= "⏰ <b>Processed:</b> " . date('Y-m-d H:i:s') . "\n\n";
            $telegram_message .= "✅ <i>Status updated by administrator</i>";
            
            $telegram_sent = sendTelegramNotification($telegram_message);
            
            // Send Enhanced Email notification for status update
            $email_subject = "⚡ Exchange Request $new_status - $request_id - " . SITE_NAME;
            $email_message = "Exchange request status has been updated!\n\n";
            $email_message .= "REQUEST UPDATE DETAILS:\n";
            $email_message .= "───────────────────────\n";
            $email_message .= "Request ID: $request_id\n";
            $email_message .= "Previous Status: $old_status\n";
            $email_message .= "New Status: $new_status\n";
            $email_message .= "Customer Name: {$request['name']}\n";
            $email_message .= "Phone Number: {$request['phone']}\n";
            $email_message .= "Email Address: {$request['email']}\n";
            $email_message .= "Exchange: {$request['amount']} {$request['crypto']}\n";
            $email_message .= "Naira Equivalent: ₦" . number_format($request['naira_amount'], 2) . "\n";
            $email_message .= "Bank: {$request['bank_name']}\n";
            $email_message .= "Account: {$request['account_number']}\n";
            $email_message .= "Admin Notes: $admin_notes\n";
            $email_message .= "Processed Time: " . date('Y-m-d H:i:s') . "\n\n";
            $email_message .= "ACTION PERFORMED:\n";
            $email_message .= "────────────────\n";
            $email_message .= "This status change was performed through the admin panel.\n";
            $email_message .= "The customer has been notified of the status update.";
            
            $email_sent = sendEmailNotification($email_subject, $email_message);
            
            // Log notification results
            if ($telegram_sent) {
                error_log("Telegram status update notification sent for request: $request_id");
            } else {
                error_log("Failed to send Telegram status update notification for request: $request_id");
            }
            
            if ($email_sent) {
                error_log("Email status update notification sent for request: $request_id");
            } else {
                error_log("Failed to send email status update notification for request: $request_id");
            }
            
            // Send notification to customer about status update
            $customer_email_subject = "📢 Update on Your Exchange Request - " . SITE_NAME;
            $customer_email_message = "Dear {$request['name']},\n\n";
            $customer_email_message .= "Your exchange request status has been updated!\n\n";
            $customer_email_message .= "REQUEST UPDATE:\n";
            $customer_email_message .= "───────────────\n";
            $customer_email_message .= "Request ID: $request_id\n";
            $customer_email_message .= "New Status: $new_status\n";
            $customer_email_message .= "Cryptocurrency: {$request['amount']} {$request['crypto']}\n";
            $customer_email_message .= "Naira Amount: ₦" . number_format($request['naira_amount'], 2) . "\n\n";
            
            if ($new_status === 'completed') {
                $customer_email_message .= "🎉 Your exchange has been completed! The funds should reflect in your bank account shortly.\n\n";
            } elseif ($new_status === 'cancelled') {
                $customer_email_message .= "❌ Your exchange request has been cancelled. " . ($admin_notes ? "Reason: $admin_notes" : "") . "\n\n";
            } else {
                $customer_email_message .= "🔄 Your request is being processed. We'll notify you when there are further updates.\n\n";
            }
            
            if ($admin_notes) {
                $customer_email_message .= "Admin Notes: $admin_notes\n\n";
            }
            
            $customer_email_message .= "Thank you for choosing " . SITE_NAME . "!\n\n";
            $customer_email_message .= "Best regards,\n" . SITE_NAME . " Team";
            
            sendEmailNotification($customer_email_subject, $customer_email_message, $request['email']);
            
            $_SESSION['success'] = "Request status updated successfully!";
            header("Location: admin.php");
            exit;
        }
    }
    
    if (isset($_POST['add_crypto'])) {
        $symbol = strtoupper($_POST['symbol']);
        $name = $_POST['name'];
        $wallet_address = $_POST['wallet_address'];
        $active = isset($_POST['active']) ? true : false;
        
        // Handle QR code upload
        $qr_code = '';
        if (isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'qrcodes/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['qr_code']['name'], PATHINFO_EXTENSION);
            $filename = strtolower($symbol) . '_qr.' . $file_extension;
            $qr_code = $upload_dir . $filename;
            
            move_uploaded_file($_FILES['qr_code']['tmp_name'], $qr_code);
        }
        
        $crypto_data['cryptocurrencies'][$symbol] = [
            'name' => $name,
            'wallet_address' => $wallet_address,
            'qr_code' => $qr_code,
            'active' => $active
        ];
        
        file_put_contents($crypto_file, json_encode($crypto_data));
        
        // Send notification for new cryptocurrency added
        $telegram_message = "💰 <b>NEW CRYPTOCURRENCY ADDED</b>\n\n";
        $telegram_message .= "🔤 <b>Symbol:</b> $symbol\n";
        $telegram_message .= "📛 <b>Name:</b> $name\n";
        $telegram_message .= "🏦 <b>Wallet Address:</b> <code>" . substr($wallet_address, 0, 20) . "...</code>\n";
        $telegram_message .= "📊 <b>Status:</b> " . ($active ? "Active" : "Inactive") . "\n";
        $telegram_message .= "⏰ <b>Added:</b> " . date('Y-m-d H:i:s') . "\n\n";
        $telegram_message .= "✅ <i>New cryptocurrency added to the platform</i>";
        
        sendTelegramNotification($telegram_message);
        
        $email_subject = "💰 New Cryptocurrency Added - $symbol - " . SITE_NAME;
        $email_message = "A new cryptocurrency has been added to the platform!\n\n";
        $email_message .= "CRYPTOCURRENCY DETAILS:\n";
        $email_message .= "───────────────────────\n";
        $email_message .= "Symbol: $symbol\n";
        $email_message .= "Name: $name\n";
        $email_message .= "Wallet Address: $wallet_address\n";
        $email_message .= "Status: " . ($active ? "Active" : "Inactive") . "\n";
        $email_message .= "Added Time: " . date('Y-m-d H:i:s') . "\n\n";
        $email_message .= "This cryptocurrency is now " . ($active ? "available" : "disabled") . " on the exchange platform.";
        
        sendEmailNotification($email_subject, $email_message);
        
        $_SESSION['success'] = "Cryptocurrency added successfully!";
        header("Location: admin.php");
        exit;
    }
    
    if (isset($_POST['update_crypto'])) {
        $symbol = $_POST['symbol'];
        $name = $_POST['name'];
        $wallet_address = $_POST['wallet_address'];
        $active = isset($_POST['active']) ? true : false;
        
        $old_data = $crypto_data['cryptocurrencies'][$symbol];
        
        // Handle QR code upload
        if (isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'qrcodes/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Delete old QR code if exists
            if (!empty($crypto_data['cryptocurrencies'][$symbol]['qr_code']) && 
                file_exists($crypto_data['cryptocurrencies'][$symbol]['qr_code'])) {
                unlink($crypto_data['cryptocurrencies'][$symbol]['qr_code']);
            }
            
            $file_extension = pathinfo($_FILES['qr_code']['name'], PATHINFO_EXTENSION);
            $filename = strtolower($symbol) . '_qr.' . $file_extension;
            $qr_code = $upload_dir . $filename;
            
            move_uploaded_file($_FILES['qr_code']['tmp_name'], $qr_code);
            $crypto_data['cryptocurrencies'][$symbol]['qr_code'] = $qr_code;
        }
        
        $crypto_data['cryptocurrencies'][$symbol]['name'] = $name;
        $crypto_data['cryptocurrencies'][$symbol]['wallet_address'] = $wallet_address;
        $crypto_data['cryptocurrencies'][$symbol]['active'] = $active;
        
        file_put_contents($crypto_file, json_encode($crypto_data));
        
        // Send notification for cryptocurrency update
        $changes = [];
        if ($old_data['name'] !== $name) $changes[] = "Name: {$old_data['name']} → $name";
        if ($old_data['wallet_address'] !== $wallet_address) $changes[] = "Wallet address updated";
        if ($old_data['active'] !== $active) $changes[] = "Status: " . ($old_data['active'] ? "Active" : "Inactive") . " → " . ($active ? "Active" : "Inactive");
        
        if (!empty($changes)) {
            $telegram_message = "🔄 <b>CRYPTOCURRENCY UPDATED</b>\n\n";
            $telegram_message .= "🔤 <b>Symbol:</b> $symbol\n";
            $telegram_message .= "📛 <b>Name:</b> $name\n";
            $telegram_message .= "📊 <b>Status:</b> " . ($active ? "Active" : "Inactive") . "\n";
            $telegram_message .= "⏰ <b>Updated:</b> " . date('Y-m-d H:i:s') . "\n\n";
            
            if (!empty($changes)) {
                $telegram_message .= "📝 <b>Changes:</b>\n";
                foreach ($changes as $change) {
                    $telegram_message .= "• $change\n";
                }
            }
            
            $telegram_message .= "\n✅ <i>Cryptocurrency details updated</i>";
            
            sendTelegramNotification($telegram_message);
            
            $email_subject = "🔄 Cryptocurrency Updated - $symbol - " . SITE_NAME;
            $email_message = "Cryptocurrency details have been updated!\n\n";
            $email_message .= "UPDATED CRYPTOCURRENCY:\n";
            $email_message .= "───────────────────────\n";
            $email_message .= "Symbol: $symbol\n";
            $email_message .= "Name: $name\n";
            $email_message .= "Wallet Address: $wallet_address\n";
            $email_message .= "Status: " . ($active ? "Active" : "Inactive") . "\n";
            $email_message .= "Updated Time: " . date('Y-m-d H:i:s') . "\n\n";
            
            if (!empty($changes)) {
                $email_message .= "CHANGES MADE:\n";
                $email_message .= "─────────────\n";
                foreach ($changes as $change) {
                    $email_message .= "• $change\n";
                }
                $email_message .= "\n";
            }
            
            $email_message .= "The cryptocurrency is now " . ($active ? "available" : "disabled") . " on the exchange platform.";
            
            sendEmailNotification($email_subject, $email_message);
        }
        
        $_SESSION['success'] = "Cryptocurrency updated successfully!";
        header("Location: admin.php");
        exit;
    }
    
    if (isset($_POST['delete_crypto'])) {
        $symbol = $_POST['symbol'];
        
        $crypto_name = $crypto_data['cryptocurrencies'][$symbol]['name'];
        
        // Delete QR code file if exists
        if (!empty($crypto_data['cryptocurrencies'][$symbol]['qr_code']) && 
            file_exists($crypto_data['cryptocurrencies'][$symbol]['qr_code'])) {
            unlink($crypto_data['cryptocurrencies'][$symbol]['qr_code']);
        }
        
        unset($crypto_data['cryptocurrencies'][$symbol]);
        file_put_contents($crypto_file, json_encode($crypto_data));
        
        // Send notification for cryptocurrency deletion
        $telegram_message = "🗑️ <b>CRYPTOCURRENCY DELETED</b>\n\n";
        $telegram_message .= "🔤 <b>Symbol:</b> $symbol\n";
        $telegram_message .= "📛 <b>Name:</b> $crypto_name\n";
        $telegram_message .= "⏰ <b>Deleted:</b> " . date('Y-m-d H:i:s') . "\n\n";
        $telegram_message .= "⚠️ <i>Cryptocurrency removed from the platform</i>";
        
        sendTelegramNotification($telegram_message);
        
        $email_subject = "🗑️ Cryptocurrency Deleted - $symbol - " . SITE_NAME;
        $email_message = "A cryptocurrency has been deleted from the platform!\n\n";
        $email_message .= "DELETED CRYPTOCURRENCY:\n";
        $email_message .= "───────────────────────\n";
        $email_message .= "Symbol: $symbol\n";
        $email_message .= "Name: $crypto_name\n";
        $email_message .= "Deleted Time: " . date('Y-m-d H:i:s') . "\n\n";
        $email_message .= "This cryptocurrency is no longer available on the exchange platform.";
        
        sendEmailNotification($email_subject, $email_message);
        
        $_SESSION['success'] = "Cryptocurrency deleted successfully!";
        header("Location: admin.php");
        exit;
    }
    
    if (isset($_POST['logout'])) {
        // Send logout notification
        $telegram_message = "🚪 <b>ADMIN LOGOUT</b>\n\n";
        $telegram_message .= "👤 <b>User:</b> Administrator\n";
        $telegram_message .= "⏰ <b>Time:</b> " . date('Y-m-d H:i:s') . "\n";
        $telegram_message .= "🌐 <b>IP Address:</b> " . $_SERVER['REMOTE_ADDR'] . "\n\n";
        $telegram_message .= "🔒 <i>Admin session ended</i>";
        
        sendTelegramNotification($telegram_message);
        
        session_destroy();
        header("Location: admin.php");
        exit;
    }
}

// Check for success message
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']);

// Get statistics
$total_requests = count($data['exchange_requests']);
$pending_requests = 0;
$completed_requests = 0;
$today_requests = 0;
$total_volume = 0;

foreach ($data['exchange_requests'] as $request) {
    if ($request['status'] === 'pending') $pending_requests++;
    if ($request['status'] === 'completed') $completed_requests++;
    if (date('Y-m-d') === date('Y-m-d', strtotime($request['timestamp']))) $today_requests++;
    if ($request['status'] === 'completed') $total_volume += $request['naira_amount'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - CryptoCove</title>
    <style>
        :root {
            --primary: #008751;
            --primary-dark: #006441;
            --accent: #008751;
            --background: #0d1117;
            --surface: #161b22;
            --text: #ffffff;
            --text-light: #8b949e;
            --border: #30363d;
            --error: #ff6b6b;
            --success: #00ff88;
            --warning: #ffd166;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            --radius: 12px;
            --transition: all 0.3s ease;
            --glow: 0 0 20px rgba(0, 135, 81, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: var(--background);
            color: var(--text);
            line-height: 1.6;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 135, 81, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(0, 135, 81, 0.1) 0%, transparent 20%);
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 30px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 25px 30px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, #ffffff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-icon {
            font-size: 1.8rem;
        }

        .admin-info {
            margin-top: 15px;
            padding: 15px;
            background: rgba(13, 17, 23, 0.5);
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }

        .admin-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .admin-role {
            color: var(--primary);
            font-size: 0.9rem;
        }

        .sidebar-nav {
            padding: 0 15px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            color: var(--text-light);
            text-decoration: none;
            border-radius: var(--radius);
            transition: var(--transition);
            margin-bottom: 5px;
            cursor: pointer;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(0, 135, 81, 0.1);
            color: var(--primary);
        }

        .nav-item i {
            font-size: 1.2rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .page-title {
            font-size: 2rem;
            background: linear-gradient(135deg, #ffffff 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logout-btn {
            background: rgba(255, 107, 107, 0.1);
            color: var(--error);
            border: 1px solid rgba(255, 107, 107, 0.3);
            padding: 10px 20px;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background: rgba(255, 107, 107, 0.2);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #ffffff);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--primary) 0%, #ffffff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Tables */
        .table-container {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            background: rgba(13, 17, 23, 0.5);
            font-weight: 600;
            color: var(--text-light);
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background: rgba(255, 209, 102, 0.1);
            color: var(--warning);
            border: 1px solid rgba(255, 209, 102, 0.3);
        }

        .status-completed {
            background: rgba(0, 255, 136, 0.1);
            color: var(--success);
            border: 1px solid rgba(0, 255, 136, 0.3);
        }

        .status-cancelled {
            background: rgba(255, 107, 107, 0.1);
            color: var(--error);
            border: 1px solid rgba(255, 107, 107, 0.3);
        }

        .btn {
            padding: 8px 15px;
            border-radius: var(--radius);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--glow);
        }

        .btn-danger {
            background: rgba(255, 107, 107, 0.1);
            color: var(--error);
            border: 1px solid rgba(255, 107, 107, 0.3);
        }

        .btn-danger:hover {
            background: rgba(255, 107, 107, 0.2);
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: rgba(13, 17, 23, 0.5);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 135, 81, 0.1);
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
        }

        /* Notifications */
        .notification {
            padding: 15px 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
        }

        .notification.success {
            background: rgba(0, 135, 81, 0.1);
            border: 1px solid rgba(0, 135, 81, 0.3);
            color: var(--primary);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .close-modal {
            background: none;
            border: none;
            color: var(--text-light);
            font-size: 1.5rem;
            cursor: pointer;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 1px solid var(--border);
            margin-bottom: 20px;
        }

        .tab {
            padding: 15px 25px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: var(--transition);
        }

        .tab.active {
            border-bottom-color: var(--primary);
            color: var(--primary);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Receipt Image */
        .receipt-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }

        /* Crypto Grid */
        .crypto-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .crypto-card {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            position: relative;
        }

        .crypto-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #ffffff);
        }

        .crypto-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 15px;
        }

        .crypto-symbol {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .crypto-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .crypto-status.active {
            background: rgba(0, 135, 81, 0.1);
            color: var(--primary);
            border: 1px solid rgba(0, 135, 81, 0.3);
        }

        .crypto-status.inactive {
            background: rgba(255, 107, 107, 0.1);
            color: var(--error);
            border: 1px solid rgba(255, 107, 107, 0.3);
        }

        .crypto-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <span class="logo-icon">🇳🇬</span>
                    <span>CryptoCove</span>
                </div>
                <div class="admin-info">
                    <div class="admin-name">Administrator</div>
                    <div class="admin-role">Super Admin</div>
                </div>
            </div>
            
            <div class="sidebar-nav">
                <a class="nav-item active" data-tab="dashboard">
                    <i>📊</i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-item" data-tab="requests">
                    <i>🔄</i>
                    <span>Exchange Requests</span>
                </a>
                <a class="nav-item" data-tab="cryptocurrencies">
                    <i>💰</i>
                    <span>Cryptocurrencies</span>
                </a>
                <form method="POST" style="margin-top: 20px;">
                    <button type="submit" name="logout" class="nav-item" style="width: 100%; background: none; border: none; text-align: left; color: inherit;">
                        <i>🚪</i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1 class="page-title">Admin Dashboard</h1>
                <div class="header-actions">
                    <form method="POST">
                        <button type="submit" name="logout" class="logout-btn">Logout</button>
                    </form>
                </div>
            </div>
            
            <?php if ($success): ?>
                <div class="notification success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <!-- Dashboard Tab -->
            <div class="tab-content active" id="dashboard">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $total_requests; ?></div>
                        <div class="stat-label">Total Exchange Requests</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $pending_requests; ?></div>
                        <div class="stat-label">Pending Requests</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $completed_requests; ?></div>
                        <div class="stat-label">Completed Exchanges</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $today_requests; ?></div>
                        <div class="stat-label">Today's Requests</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">₦<?php echo number_format($total_volume, 2); ?></div>
                        <div class="stat-label">Total Volume</div>
                    </div>
                </div>
                
                <div class="table-container">
                    <div class="table-header">
                        <div class="table-title">Recent Exchange Requests</div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Crypto</th>
                                <th>Amount</th>
                                <th>Naira</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recent_requests = array_slice($data['exchange_requests'], -5, 5, true);
                            $recent_requests = array_reverse($recent_requests);
                            
                            foreach ($recent_requests as $id => $request): 
                            ?>
                                <tr>
                                    <td><?php echo substr($id, 0, 8) . '...'; ?></td>
                                    <td><?php echo $request['name']; ?></td>
                                    <td><?php echo $request['crypto']; ?></td>
                                    <td><?php echo number_format($request['amount'], 6); ?></td>
                                    <td>₦<?php echo number_format($request['naira_amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $request['status']; ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, H:i', strtotime($request['timestamp'])); ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="openRequestModal('<?php echo $id; ?>')">
                                            View
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($recent_requests)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 30px; color: var(--text-light);">
                                        No exchange requests found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Requests Tab -->
            <div class="tab-content" id="requests">
                <div class="table-container">
                    <div class="table-header">
                        <div class="table-title">All Exchange Requests</div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Crypto</th>
                                <th>Amount</th>
                                <th>Naira</th>
                                <th>Bank</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $all_requests = array_reverse($data['exchange_requests']);
                            
                            foreach ($all_requests as $id => $request): 
                            ?>
                                <tr>
                                    <td><?php echo substr($id, 0, 8) . '...'; ?></td>
                                    <td><?php echo $request['name']; ?></td>
                                    <td><?php echo $request['phone']; ?></td>
                                    <td><?php echo $request['crypto']; ?></td>
                                    <td><?php echo number_format($request['amount'], 6); ?></td>
                                    <td>₦<?php echo number_format($request['naira_amount'], 2); ?></td>
                                    <td title="<?php echo $request['bank_name'] . ' - ' . $request['account_number']; ?>">
                                        <?php echo $request['bank_name']; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $request['status']; ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, H:i', strtotime($request['timestamp'])); ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="openRequestModal('<?php echo $id; ?>')">
                                            Manage
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($all_requests)): ?>
                                <tr>
                                    <td colspan="10" style="text-align: center; padding: 30px; color: var(--text-light);">
                                        No exchange requests found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Cryptocurrencies Tab -->
            <div class="tab-content" id="cryptocurrencies">
                <div class="table-header" style="margin-bottom: 20px;">
                    <div class="table-title">Manage Cryptocurrencies</div>
                    <button class="btn btn-primary" onclick="openCryptoModal()">Add New Crypto</button>
                </div>
                
                <div class="crypto-grid">
                    <?php foreach ($crypto_data['cryptocurrencies'] as $symbol => $crypto): ?>
                        <div class="crypto-card">
                            <div class="crypto-header">
                                <div class="crypto-symbol"><?php echo $symbol; ?></div>
                                <div class="crypto-status <?php echo $crypto['active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $crypto['active'] ? 'Active' : 'Inactive'; ?>
                                </div>
                            </div>
                            <div style="margin-bottom: 10px;">
                                <strong>Name:</strong> <?php echo $crypto['name']; ?>
                            </div>
                            <div style="margin-bottom: 10px; word-break: break-all;">
                                <strong>Wallet:</strong> <?php echo $crypto['wallet_address']; ?>
                            </div>
                            <?php if (!empty($crypto['qr_code']) && file_exists($crypto['qr_code'])): ?>
                                <div style="margin-bottom: 10px;">
                                    <strong>QR Code:</strong> <img src="<?php echo $crypto['qr_code']; ?>" style="max-width: 100px; border-radius: 5px; border: 1px solid var(--border);">
                                </div>
                            <?php endif; ?>
                            <div class="crypto-actions">
                                <button class="btn btn-primary btn-sm" onclick="editCrypto('<?php echo $symbol; ?>')">Edit</button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="delete_crypto" value="1">
                                    <input type="hidden" name="symbol" value="<?php echo $symbol; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this cryptocurrency?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Request Modal -->
    <div class="modal" id="requestModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Manage Exchange Request</div>
                <button class="close-modal" onclick="closeRequestModal()">&times;</button>
            </div>
            <div id="modalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
    
    <!-- Crypto Modal -->
    <div class="modal" id="cryptoModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="cryptoModalTitle">Add Cryptocurrency</div>
                <button class="close-modal" onclick="closeCryptoModal()">&times;</button>
            </div>
            <form method="POST" id="cryptoForm" enctype="multipart/form-data">
                <input type="hidden" name="add_crypto" value="1" id="cryptoAction">
                <input type="hidden" name="symbol" id="cryptoSymbol">
                
                <div class="form-group">
                    <label class="form-label">Symbol (e.g., BTC, ETH)</label>
                    <input type="text" name="symbol" class="form-control" required id="symbolInput">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Name (e.g., Bitcoin, Ethereum)</label>
                    <input type="text" name="name" class="form-control" required id="nameInput">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Wallet Address</label>
                    <input type="text" name="wallet_address" class="form-control" required id="walletInput">
                </div>
                
                <div class="form-group">
                    <label class="form-label">QR Code Image</label>
                    <input type="file" name="qr_code" class="form-control" accept="image/*" id="qrInput">
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="active" class="form-check-input" id="activeInput" checked>
                        <label class="form-label" style="margin: 0;">Active (show on website)</label>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn" onclick="closeCryptoModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Cryptocurrency</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tab functionality
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs
                document.querySelectorAll('.nav-item').forEach(nav => {
                    nav.classList.remove('active');
                });
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Show corresponding content
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Modal functions
        function openRequestModal(requestId) {
            const modal = document.getElementById('requestModal');
            const modalBody = document.getElementById('modalBody');
            
            // Show loading
            modalBody.innerHTML = '<div style="text-align: center; padding: 30px;">Loading...</div>';
            modal.style.display = 'flex';
            
            // Fetch request details (in a real app, this would be an AJAX call)
            // For this example, we'll use the data from PHP
            const request = <?php echo json_encode($data['exchange_requests']); ?>[requestId];
            
            if (request) {
                let receiptHtml = '';
                if (request.receipt_path && request.receipt_path !== '') {
                    receiptHtml = `
                        <div class="form-group">
                            <label class="form-label">Transaction Receipt</label>
                            <div>
                                <img src="${request.receipt_path}" class="receipt-image" alt="Transaction Receipt">
                            </div>
                        </div>
                    `;
                }
                
                modalBody.innerHTML = `
                    <div style="margin-bottom: 20px;">
                        <div class="form-group">
                            <label class="form-label">Request ID</label>
                            <div class="form-control" style="background: rgba(13, 17, 23, 0.3);">${requestId}</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Customer Information</label>
                            <div class="form-control" style="background: rgba(13, 17, 23, 0.3);">
                                ${request.name}<br>
                                ${request.email}<br>
                                ${request.phone}
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Exchange Details</label>
                            <div class="form-control" style="background: rgba(13, 17, 23, 0.3);">
                                ${request.amount} ${request.crypto}<br>
                                → ₦${request.naira_amount.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Bank Details</label>
                            <div class="form-control" style="background: rgba(13, 17, 23, 0.3);">
                                ${request.bank_name}<br>
                                ${request.account_number}<br>
                                ${request.account_name}
                            </div>
                        </div>
                        
                        ${receiptHtml}
                    </div>
                    
                    <form method="POST" id="statusForm">
                        <input type="hidden" name="update_status" value="1">
                        <input type="hidden" name="request_id" value="${requestId}">
                        
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="pending" ${request.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="completed" ${request.status === 'completed' ? 'selected' : ''}>Completed</option>
                                <option value="cancelled" ${request.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Admin Notes</label>
                            <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add any notes about this exchange...">${request.admin_notes || ''}</textarea>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn" onclick="closeRequestModal()">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                `;
            } else {
                modalBody.innerHTML = '<div style="text-align: center; padding: 30px; color: var(--error);">Request not found.</div>';
            }
        }
        
        function closeRequestModal() {
            document.getElementById('requestModal').style.display = 'none';
        }
        
        function openCryptoModal() {
            document.getElementById('cryptoModalTitle').textContent = 'Add Cryptocurrency';
            document.getElementById('cryptoAction').value = 'add_crypto';
            document.getElementById('cryptoSymbol').value = '';
            document.getElementById('symbolInput').value = '';
            document.getElementById('nameInput').value = '';
            document.getElementById('walletInput').value = '';
            document.getElementById('qrInput').value = '';
            document.getElementById('activeInput').checked = true;
            document.getElementById('symbolInput').readOnly = false;
            
            document.getElementById('cryptoModal').style.display = 'flex';
        }
        
        function editCrypto(symbol) {
            const crypto = <?php echo json_encode($crypto_data['cryptocurrencies']); ?>[symbol];
            
            if (crypto) {
                document.getElementById('cryptoModalTitle').textContent = 'Edit Cryptocurrency';
                document.getElementById('cryptoAction').value = 'update_crypto';
                document.getElementById('cryptoSymbol').value = symbol;
                document.getElementById('symbolInput').value = symbol;
                document.getElementById('nameInput').value = crypto.name;
                document.getElementById('walletInput').value = crypto.wallet_address;
                document.getElementById('activeInput').checked = crypto.active;
                document.getElementById('symbolInput').readOnly = true;
                
                document.getElementById('cryptoModal').style.display = 'flex';
            }
        }
        
        function closeCryptoModal() {
            document.getElementById('cryptoModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            const requestModal = document.getElementById('requestModal');
            const cryptoModal = document.getElementById('cryptoModal');
            
            if (e.target === requestModal) {
                closeRequestModal();
            }
            
            if (e.target === cryptoModal) {
                closeCryptoModal();
            }
        });
    </script>
</body>
</html>