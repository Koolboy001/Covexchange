<?php
// Platform-agnostic configuration
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 1 for debugging, 0 for production

// Set timezone
date_default_timezone_set('Africa/Lagos');

// Check if we're in a production environment
$isProduction = (getenv('APP_ENV') === 'production' || !file_exists('.local'));

// Enable error logging in production
if ($isProduction) {
    ini_set('log_errors', 1);
    ini_set('error_log', 'php://stderr');
}


// config.php - Configuration and core functions
session_start();

// Telegram Bot Configuration
define('TELEGRAM_BOT_TOKEN', '8439229450:AAFg8z-Ijca7Y2LWyI5-Z87Rv8ZVxs3AJ5Q');
define('TELEGRAM_CHAT_ID', '8330683037');

// Email Configuration
define('ADMIN_EMAIL', 'aa9769850@gmail.com');
define('SITE_NAME', 'CryptoCove');

// Database simulation using files (in production, use MySQL)
$data_file = 'exchange_data.json';
$crypto_file = 'cryptocurrencies.json';

// Initialize data files if they don't exist
if (!file_exists($data_file)) {
    $initial_data = [
        'exchange_requests' => [],
        'settings' => []
    ];
    file_put_contents($data_file, json_encode($initial_data));
}

if (!file_exists($crypto_file)) {
    $initial_crypto = [
        'cryptocurrencies' => [
            'BTC' => [
                'name' => 'Bitcoin',
                'wallet_address' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
                'qr_code' => 'btc_qr.png',
                'active' => true
            ],
            'ETH' => [
                'name' => 'Ethereum', 
                'wallet_address' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F',
                'qr_code' => 'eth_qr.png',
                'active' => true
            ],
            'USDT' => [
                'name' => 'Tether',
                'wallet_address' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F',
                'qr_code' => 'usdt_qr.png',
                'active' => true
            ],
            'BNB' => [
                'name' => 'Binance Coin',
                'wallet_address' => 'bnb1xy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
                'qr_code' => 'bnb_qr.png',
                'active' => true
            ]
        ]
    ];
    file_put_contents($crypto_file, json_encode($initial_crypto));
}

// Load data
$data = json_decode(file_get_contents($data_file), true);
$crypto_data = json_decode(file_get_contents($crypto_file), true);

// Function to get current crypto prices from multiple reliable APIs
function getCryptoPrices() {
    $prices = [];
    
    // Try multiple APIs in sequence until we get successful data
    $apis_tried = [];
    
    // API 1: CoinGecko (Most reliable free API)
    $apis_tried[] = 'CoinGecko';
    $coingecko_prices = getPricesFromCoinGecko();
    if ($coingecko_prices) {
        return $coingecko_prices;
    }
    
    // API 2: CoinMarketCap (Alternative)
    $apis_tried[] = 'CoinMarketCap';
    $coinmarketcap_prices = getPricesFromCoinMarketCap();
    if ($coinmarketcap_prices) {
        return $coinmarketcap_prices;
    }
    
    // API 3: CryptoCompare
    $apis_tried[] = 'CryptoCompare';
    $cryptocompare_prices = getPricesFromCryptoCompare();
    if ($cryptocompare_prices) {
        return $cryptocompare_prices;
    }
    
    // API 4: Manual calculation with reliable USD/NGN rate
    $apis_tried[] = 'Manual USD/NGN';
    $manual_prices = getPricesManual();
    if ($manual_prices) {
        return $manual_prices;
    }
    
    // If all APIs fail, use the last known prices from cache
    $cache_file = 'price_cache.json';
    if (file_exists($cache_file)) {
        $cached_prices = json_decode(file_get_contents($cache_file), true);
        if ($cached_prices && count($cached_prices) > 0) {
            error_log("All APIs failed. Using cached prices. APIs tried: " . implode(', ', $apis_tried));
            return $cached_prices;
        }
    }
    
    // Final fallback - hardcoded reasonable prices
    error_log("All price APIs failed, using hardcoded fallback prices");
    return [
        'BTC' => 42000,
        'ETH' => 2500,
        'BNB' => 320,
        'USDT_NGN' => 1440
    ];
}

// CoinGecko API (Free, no API key needed)
function getPricesFromCoinGecko() {
    $prices = [];
    
    try {
        // Get crypto prices in USD
        $url = "https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,binancecoin,tether&vs_currencies=usd";
        $response = @file_get_contents($url, false, stream_context_create([
            'http' => ['timeout' => 5]
        ]));
        
        if ($response !== false) {
            $data = json_decode($response, true);
            
            if (isset($data['bitcoin']['usd'])) {
                $prices['BTC'] = $data['bitcoin']['usd'];
            }
            if (isset($data['ethereum']['usd'])) {
                $prices['ETH'] = $data['ethereum']['usd'];
            }
            if (isset($data['binancecoin']['usd'])) {
                $prices['BNB'] = $data['binancecoin']['usd'];
            }
            if (isset($data['tether']['usd'])) {
                $prices['USDT'] = $data['tether']['usd'];
            }
            
            // Get USD to NGN rate from a reliable source
            $ngn_rate = getUSDToNGNRate();
            if ($ngn_rate) {
                $prices['USDT_NGN'] = $ngn_rate - 60; // Apply 60 Naira discount
                
                // Save successful prices to cache
                file_put_contents('price_cache.json', json_encode($prices));
                return $prices;
            }
        }
    } catch (Exception $e) {
        error_log("CoinGecko API error: " . $e->getMessage());
    }
    
    return false;
}

// CoinMarketCap API (Free tier available)
function getPricesFromCoinMarketCap() {
    $prices = [];
    
    try {
        // You would need an API key for CoinMarketCap
        // For now, we'll use a similar approach to CoinGecko
        // This is a placeholder for when you get a CoinMarketCap API key
        
        return false; // Remove this line when implementing
        
        $api_key = 'YOUR_COINMARKETCAP_API_KEY';
        $url = "https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest?symbol=BTC,ETH,BNB,USDT&convert=USD";
        
        $response = @file_get_contents($url, false, stream_context_create([
            'http' => [
                'header' => "X-CMC_PRO_API_KEY: $api_key\r\n",
                'timeout' => 5
            ]
        ]));
        
        if ($response !== false) {
            $data = json_decode($response, true);
            // Process CoinMarketCap data here
        }
    } catch (Exception $e) {
        error_log("CoinMarketCap API error: " . $e->getMessage());
    }
    
    return false;
}

// CryptoCompare API
function getPricesFromCryptoCompare() {
    $prices = [];
    
    try {
        $url = "https://min-api.cryptocompare.com/data/pricemulti?fsyms=BTC,ETH,BNB,USDT&tsyms=USD";
        $response = @file_get_contents($url, false, stream_context_create([
            'http' => ['timeout' => 5]
        ]));
        
        if ($response !== false) {
            $data = json_decode($response, true);
            
            if (isset($data['BTC']['USD'])) {
                $prices['BTC'] = $data['BTC']['USD'];
            }
            if (isset($data['ETH']['USD'])) {
                $prices['ETH'] = $data['ETH']['USD'];
            }
            if (isset($data['BNB']['USD'])) {
                $prices['BNB'] = $data['BNB']['USD'];
            }
            if (isset($data['USDT']['USD'])) {
                $prices['USDT'] = $data['USDT']['USD'];
            }
            
            $ngn_rate = getUSDToNGNRate();
            if ($ngn_rate && count($prices) > 0) {
                $prices['USDT_NGN'] = $ngn_rate - 60;
                file_put_contents('price_cache.json', json_encode($prices));
                return $prices;
            }
        }
    } catch (Exception $e) {
        error_log("CryptoCompare API error: " . $e->getMessage());
    }
    
    return false;
}

// Manual calculation with reliable USD/NGN sources
function getPricesManual() {
    $prices = [];
    
    try {
        // Get reliable USD/NGN rate from multiple sources
        $ngn_rate = getUSDToNGNRate();
        if (!$ngn_rate) {
            return false;
        }
        
        // Use reasonable estimated crypto prices if APIs fail
        $prices = [
            'BTC' => 42000,
            'ETH' => 2500,
            'BNB' => 320,
            'USDT' => 1,
            'USDT_NGN' => $ngn_rate - 60
        ];
        
        file_put_contents('price_cache.json', json_encode($prices));
        return $prices;
        
    } catch (Exception $e) {
        error_log("Manual price calculation error: " . $e->getMessage());
    }
    
    return false;
}

// Get USD to NGN rate from reliable sources
function getUSDToNGNRate() {
    // Try multiple sources for USD/NGN rate
    
    // Source 1: FreeCurrencyAPI
    try {
        $url = "https://api.freecurrencyapi.com/v1/latest?apikey=fca_live_2a5a6a1a1e5d7b5a5a5a5a5a5a5a5a5a5a5a5a5a&currencies=NGN&base_currency=USD";
        $response = @file_get_contents($url, false, stream_context_create([
            'http' => ['timeout' => 5]
        ]));
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['data']['NGN'])) {
                return floatval($data['data']['NGN']);
            }
        }
    } catch (Exception $e) {
        error_log("FreeCurrencyAPI error: " . $e->getMessage());
    }
    
    // Source 2: ExchangeRate-API
    try {
        $url = "https://api.exchangerate-api.com/v4/latest/USD";
        $response = @file_get_contents($url, false, stream_context_create([
            'http' => ['timeout' => 5]
        ]));
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['rates']['NGN'])) {
                return floatval($data['rates']['NGN']);
            }
        }
    } catch (Exception $e) {
        error_log("ExchangeRate-API error: " . $e->getMessage());
    }
    
    // Source 3: Fixer.io (requires API key)
    // Source 4: CurrencyFreaks (requires API key)
    
    // Final fallback - reasonable estimate
    return 1500; // Conservative estimate of 1500 NGN per USD
}

// Enhanced Telegram notification function
function sendTelegramNotification($message) {
    $bot_token = '8439229450:AAFg8z-Ijca7Y2LWyI5-Z87Rv8ZVxs3AJ5Q';
    $chat_id = '8330683037';
    
    // Remove the problematic condition that was blocking notifications
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
            error_log("Telegram notification failed to send. Please check bot token and chat ID.");
            return false;
        }
        
        $response = json_decode($result, true);
        if (!$response['ok']) {
            error_log("Telegram API error: " . ($response['description'] ?? 'Unknown error'));
            return false;
        }
        
        error_log("Telegram notification sent successfully!");
        return true;
    } catch (Exception $e) {
        error_log("Telegram notification error: " . $e->getMessage());
        return false;
    }
}

// Enhanced Email notification function
function sendEmailNotification($subject, $message, $to = null) {
    if ($to === null) {
        $to = 'aa9769850@gmail.com';
    }
    
    try {
        $headers = "From: CryptoCove <noreply@cryptocove.com>\r\n";
        $headers .= "Reply-To: noreply@cryptocove.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "X-Priority: 1 (Highest)\r\n";
        
        $html_message = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
                .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: #008751; color: white; padding: 20px; border-radius: 8px 8px 0 0; margin: -30px -30px 20px -30px; }
                .content { line-height: 1.6; }
                .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🔄 CryptoCove Notification</h2>
                </div>
                <div class='content'>
                    " . nl2br(htmlspecialchars($message)) . "
                </div>
                <div class='footer'>
                    <p>This is an automated notification from CryptoCove Exchange Platform.</p>
                    <p>© " . date('Y') . " CryptoCove. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $success = mail($to, $subject, $html_message, $headers);
        
        if ($success) {
            error_log("Email notification sent successfully to: $to");
        } else {
            error_log("Email notification failed to send to: $to");
        }
        
        return $success;
    } catch (Exception $e) {
        error_log("Email notification error: " . $e->getMessage());
        return false;
    }
}

// Handle form submissions for exchange requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'exchange_request') {
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $crypto = $_POST['crypto'];
        $amount = floatval($_POST['amount']);
        $bank_name = htmlspecialchars(trim($_POST['bank_name']));
        $account_number = htmlspecialchars(trim($_POST['account_number']));
        $account_name = htmlspecialchars(trim($_POST['account_name']));
        
        // Handle file upload
        $receipt_path = '';
        if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
            
            if (in_array(strtolower($file_extension), $allowed_extensions)) {
                $filename = uniqid('receipt_') . '.' . $file_extension;
                $receipt_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['receipt']['tmp_name'], $receipt_path)) {
                    // File uploaded successfully
                } else {
                    error_log("Failed to move uploaded file");
                    $receipt_path = '';
                }
            }
        }
        
        // Get current prices
        $current_prices = getCryptoPrices();
        $crypto_price = $current_prices[$crypto] ?? 0;
        $usdt_ngn_rate = $current_prices['USDT_NGN'] ?? 1440;
        
        // Calculate Naira equivalent with discounted rate
        if ($crypto === 'USDT') {
            $naira_amount = $amount * $usdt_ngn_rate;
        } else {
            $naira_amount = $amount * $crypto_price * $usdt_ngn_rate;
        }
        
        // Create exchange request
        $request_id = uniqid('EXCH_');
        $exchange_request = [
            'id' => $request_id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'crypto' => $crypto,
            'amount' => $amount,
            'crypto_price' => $crypto_price,
            'usdt_ngn_rate' => $usdt_ngn_rate,
            'naira_amount' => $naira_amount,
            'bank_name' => $bank_name,
            'account_number' => $account_number,
            'account_name' => $account_name,
            'receipt_path' => $receipt_path,
            'status' => 'pending',
            'timestamp' => date('Y-m-d H:i:s'),
            'admin_notes' => ''
        ];
        
        // Save to data
        $data['exchange_requests'][$request_id] = $exchange_request;
        file_put_contents($data_file, json_encode($data));
        
        // After creating the exchange request, add this notification section:

// Send Enhanced Telegram notification
$telegram_message = "🔄 NEW EXCHANGE REQUEST\n\n";
$telegram_message .= "📋 Request ID: $request_id\n";
$telegram_message .= "👤 Customer: $name\n";
$telegram_message .= "📞 Phone: $phone\n";
$telegram_message .= "📧 Email: $email\n";
$telegram_message .= "💰 Exchange: $amount $crypto\n";
$telegram_message .= "💵 Naira Equivalent: ₦" . number_format($naira_amount, 2) . "\n";
$telegram_message .= "🏦 Bank: $bank_name\n";
$telegram_message .= "🔢 Account: $account_number\n";
$telegram_message .= "👤 Account Name: $account_name\n";
$telegram_message .= "⏰ Time: " . date('Y-m-d H:i:s');

$telegram_sent = sendTelegramNotification($telegram_message);

// Send Enhanced Email notification
$email_subject = "🔄 New Exchange Request - $request_id";
$email_message = "You have received a new exchange request!\n\n";
$email_message .= "REQUEST DETAILS:\n";
$email_message .= "────────────────\n";
$email_message .= "Request ID: $request_id\n";
$email_message .= "Customer Name: $name\n";
$email_message .= "Phone Number: $phone\n";
$email_message .= "Email Address: $email\n";
$email_message .= "Cryptocurrency: $amount $crypto\n";
$email_message .= "Naira Equivalent: ₦" . number_format($naira_amount, 2) . "\n";
$email_message .= "Bank Name: $bank_name\n";
$email_message .= "Account Number: $account_number\n";
$email_message .= "Account Name: $account_name\n";
$email_message .= "Submission Time: " . date('Y-m-d H:i:s') . "\n\n";
$email_message .= "Please log in to the admin panel to process this request.";

$email_sent = sendEmailNotification($email_subject, $email_message);

// Log notification results for debugging
error_log("Exchange request submitted - ID: $request_id");
error_log("Telegram notification: " . ($telegram_sent ? "SENT" : "FAILED"));
error_log("Email notification: " . ($email_sent ? "SENT" : "FAILED"));
        
        // Also send confirmation email to customer
        $customer_email_subject = "✅ Exchange Request Received - " . SITE_NAME;
        $customer_email_message = "Dear $name,\n\n";
        $customer_email_message .= "Thank you for your exchange request with " . SITE_NAME . "!\n\n";
        $customer_email_message .= "REQUEST SUMMARY:\n";
        $customer_email_message .= "────────────────\n";
        $customer_email_message .= "Request ID: $request_id\n";
        $customer_email_message .= "Cryptocurrency: $amount $crypto\n";
        $customer_email_message .= "Expected Naira: ₦" . number_format($naira_amount, 2) . "\n";
        $customer_email_message .= "Bank: $bank_name\n";
        $customer_email_message .= "Account: $account_number\n\n";
        $customer_email_message .= "NEXT STEPS:\n";
        $customer_email_message .= "───────────\n";
        $customer_email_message .= "1. Send your $crypto to the wallet address shown on our website\n";
        $customer_email_message .= "2. Include your Request ID ($request_id) in the transaction memo\n";
        $customer_email_message .= "3. We will process your request once we confirm the transaction\n\n";
        $customer_email_message .= "Processing time: 5-15 minutes after transaction confirmation.\n\n";
        $customer_email_message .= "Need help? Contact our support team.\n\n";
        $customer_email_message .= "Best regards,\n" . SITE_NAME . " Team";
        
        sendEmailNotification($customer_email_subject, $customer_email_message, $email);
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Check for success message
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']);

// Get current prices for display
$current_prices = getCryptoPrices();

// Debug: Check if prices are loading
if (empty($current_prices)) {
    error_log("CRITICAL: No crypto prices could be loaded from any API");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptoCove - Convert Crypto to Naira</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🇳🇬</text></svg>">
    <style>
        /* Your existing CSS styles remain exactly the same */
        :root {
            --primary: #008751;
            --primary-dark: #006441;
            --primary-light: #00a862;
            --accent: #008751;
            --accent-dark: #006441;
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
            --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            --glow: 0 0 20px rgba(0, 135, 81, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--background);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 135, 81, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(0, 135, 81, 0.1) 0%, transparent 20%);
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(90deg, var(--background) 21px, transparent 1%) center,
                linear-gradient(var(--background) 21px, transparent 1%) center,
                var(--accent);
            background-size: 22px 22px;
            opacity: 0.03;
            z-index: -1;
        }

        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background: rgba(13, 17, 23, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: var(--transition);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, #ffffff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            z-index: 1001;
        }

        .logo::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), #ffffff);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.5s ease;
        }

        .logo:hover::after {
            transform: scaleX(1);
        }

        .logo-icon {
            font-size: 2rem;
            filter: drop-shadow(0 0 10px rgba(0, 135, 81, 0.5));
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            color: var(--text);
            font-weight: 500;
            padding: 10px 0;
            position: relative;
            transition: var(--transition);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-links a.active {
            color: var(--primary);
        }

        .nav-links a.active::after {
            width: 100%;
        }

        .admin-link {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 10px 20px;
            border-radius: 30px;
            color: white;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: var(--glow);
        }

        .admin-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 25px rgba(0, 135, 81, 0.5);
        }

        /* Mobile Menu Styles - Right to Left Slide */
        .mobile-menu-btn {
            display: none;
            background: none;
            color: var(--text);
            font-size: 1.5rem;
            z-index: 1001;
            border: none;
            cursor: pointer;
        }

        .mobile-menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .mobile-menu-overlay.active {
            display: block;
            opacity: 1;
        }

        .mobile-nav {
            position: fixed;
            top: 0;
            right: -100%;
            width: 65%;
            height: 100%;
            background: var(--surface);
            z-index: 1000;
            transition: right 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            overflow-y: auto;
            padding: 80px 30px 30px;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.3);
        }

        .mobile-nav.active {
            right: 0;
        }

        .mobile-nav-links {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
        }

        .mobile-nav-links a {
            color: var(--text);
            font-size: 1.2rem;
            font-weight: 500;
            padding: 15px 0;
            border-bottom: 1px solid var(--border);
            transition: var(--transition);
        }

        .mobile-nav-links a:hover {
            color: var(--primary);
            transform: translateX(10px);
        }

        .mobile-nav-links a.active {
            color: var(--primary);
        }

        .mobile-admin-link {
            display: block;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 15px 20px;
            border-radius: var(--radius);
            color: white;
            font-weight: 600;
            text-align: center;
            margin-top: 20px;
            transition: var(--transition);
            box-shadow: var(--glow);
        }

        .mobile-admin-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 135, 81, 0.4);
        }

        /* Hero Section */
        .hero {
            padding: 160px 0 100px;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .hero-text {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(0, 135, 81, 0.1);
            color: var(--primary);
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 135, 81, 0.3);
            backdrop-filter: blur(10px);
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 24px;
            line-height: 1.1;
            background: linear-gradient(135deg, #ffffff 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
        }

        .hero h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #ffffff);
            border-radius: 2px;
        }

        .hero p {
            font-size: 1.2rem;
            color: var(--text-light);
            margin-bottom: 40px;
            max-width: 500px;
        }

        .hero-stats {
            display: flex;
            gap: 40px;
            margin-top: 50px;
        }

        .stat {
            text-align: center;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, #ffffff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .hero-visual {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .floating-card {
            width: 300px;
            height: 180px;
            background: linear-gradient(135deg, var(--surface) 0%, rgba(22, 27, 34, 0.8) 100%);
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
            animation: float 6s ease-in-out infinite;
        }

        .floating-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #ffffff);
        }

        .floating-card:nth-child(1) {
            transform: rotate(-5deg);
            z-index: 3;
        }

        .floating-card:nth-child(2) {
            position: absolute;
            transform: rotate(5deg);
            z-index: 2;
            opacity: 0.8;
        }

        .floating-card:nth-child(3) {
            position: absolute;
            transform: rotate(10deg);
            z-index: 1;
            opacity: 0.6;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(-5deg); }
            50% { transform: translateY(-20px) rotate(-5deg); }
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1rem;
            color: var(--text-light);
        }

        .crypto-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 1.2rem;
        }

        .card-balance {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .card-change {
            font-size: 0.9rem;
            color: var(--success);
        }

        /* Exchange Section */
        .exchange-section {
            padding: 100px 0;
            position: relative;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-badge {
            display: inline-block;
            background: rgba(0, 135, 81, 0.1);
            color: var(--primary);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 16px;
            border: 1px solid rgba(0, 135, 81, 0.3);
        }

        .section-title {
            font-size: 2.5rem;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #ffffff 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
        }

        .exchange-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .exchange-form-container {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 40px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .exchange-form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #ffffff);
        }

        .form-title {
            font-size: 1.5rem;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-title i {
            color: var(--primary);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: var(--text-light);
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
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

        .crypto-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .crypto-option {
            background: rgba(13, 17, 23, 0.5);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .crypto-option:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .crypto-option.selected {
            border-color: var(--primary);
            background: rgba(0, 135, 81, 0.1);
            box-shadow: var(--glow);
        }

        .crypto-icon-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            color: white;
        }

        .price-display {
            background: rgba(0, 135, 81, 0.1);
            border: 1px solid rgba(0, 135, 81, 0.3);
            border-radius: var(--radius);
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .price-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .price-label {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .btn {
            padding: 16px 32px;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            width: 100%;
            box-shadow: var(--glow);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 135, 81, 0.4);
        }

        /* Wallets Section */
        .wallets-section {
            padding: 80px 0;
        }

        .wallets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .wallet-card {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .wallet-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #ffffff);
        }

        .wallet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
        }

        .wallet-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .wallet-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 1.5rem;
        }

        .wallet-name {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .wallet-address {
            background: rgba(13, 17, 23, 0.5);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 15px;
            font-family: monospace;
            font-size: 0.9rem;
            margin-bottom: 20px;
            position: relative;
            word-break: break-all;
        }

        .qr-code {
            text-align: center;
            margin-bottom: 20px;
        }

        .qr-code img {
            max-width: 200px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }

        .copy-btn {
            background: rgba(0, 135, 81, 0.1);
            color: var(--primary);
            border: 1px solid rgba(0, 135, 81, 0.3);
            padding: 8px 15px;
            border-radius: var(--radius);
            font-size: 0.8rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .copy-btn:hover {
            background: rgba(0, 135, 81, 0.2);
        }

        /* Price Ticker */
        .price-ticker {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 40px;
        }

        .ticker-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .ticker-title {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .ticker-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .ticker-item {
            background: rgba(13, 17, 23, 0.5);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 15px;
            text-align: center;
        }

        .ticker-crypto {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .ticker-symbol {
            font-weight: 600;
        }

        .ticker-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .ticker-naira {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        /* Footer */
        footer {
            background: var(--surface);
            padding: 80px 0 30px;
            border-top: 1px solid var(--border);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 50px;
            margin-bottom: 50px;
        }

        .footer-column h3 {
            font-size: 1.3rem;
            margin-bottom: 25px;
            color: var(--primary);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: var(--text-light);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-links a:hover {
            color: var(--primary);
            transform: translateX(5px);
        }

        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid var(--border);
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Notifications */
        .notification {
            position: fixed;
            top: 30px;
            right: 30px;
            padding: 20px 25px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transform: translateX(150%);
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            z-index: 10000;
            max-width: 400px;
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            background: rgba(0, 135, 81, 0.1);
            border-color: rgba(0, 135, 81, 0.3);
            color: var(--primary);
        }

        .notification.error {
            background: rgba(255, 107, 107, 0.1);
            border-color: rgba(255, 107, 107, 0.3);
            color: var(--error);
        }

        /* Rate Discount Notice */
        .rate-notice {
            background: rgba(255, 209, 102, 0.1);
            border: 1px solid rgba(255, 209, 102, 0.3);
            border-radius: var(--radius);
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .rate-notice strong {
            color: var(--warning);
        }

        /* Advanced Animations */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        .updating::after {
            content: ' (Updating...)';
            font-size: 0.8em;
            opacity: 0.7;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ==================== */
        /* RESPONSIVE DESIGN */
        /* ==================== */

        /* Large Screens (1200px and above) */
        @media (min-width: 1200px) {
            .mobile-nav {
                width: 50%;
            }
        }

        /* Tablets and Small Laptops (1024px to 1199px) */
        @media (max-width: 1199px) {
            .hero h1 {
                font-size: 3rem;
            }
            
            .exchange-container {
                gap: 30px;
            }
            
            .floating-card {
                width: 280px;
                height: 170px;
            }
        }

        /* Tablets (768px to 1023px) */
        @media (max-width: 1023px) {
            .hero-content {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            
            .exchange-container {
                grid-template-columns: 1fr;
            }
            
            .hero h1 {
                font-size: 2.8rem;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .floating-card {
                width: 260px;
                height: 160px;
            }
            
            .footer-content {
                grid-template-columns: repeat(2, 1fr);
                gap: 40px;
            }
        }

        /* Small Tablets and Large Phones (600px to 767px) */
        @media (max-width: 767px) {
            .nav-links, .admin-link {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }

            .hero {
                padding: 140px 0 80px;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }

            .hero-stats {
                flex-direction: column;
                gap: 20px;
            }

            .floating-card {
                width: 250px;
                height: 150px;
            }

            .crypto-selector {
                grid-template-columns: 1fr;
            }
            
            .exchange-form-container {
                padding: 30px 25px;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .wallets-grid {
                grid-template-columns: 1fr;
            }
            
            .ticker-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .container {
                padding: 0 15px;
            }
        }

        /* Mobile Phones (480px to 599px) */
        @media (max-width: 599px) {
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .hero-badge {
                font-size: 0.8rem;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
            
            .section-subtitle {
                font-size: 1rem;
            }
            
            .exchange-form-container {
                padding: 25px 20px;
            }
            
            .form-title {
                font-size: 1.3rem;
            }
            
            .btn {
                padding: 14px 28px;
                font-size: 0.9rem;
            }
            
            .ticker-grid {
                grid-template-columns: 1fr;
            }
            
            .price-ticker {
                padding: 20px;
            }
            
            .wallet-card {
                padding: 25px 20px;
            }
            
            .notification {
                right: 15px;
                left: 15px;
                max-width: none;
            }
        }

        /* Small Mobile Phones (under 480px) */
        @media (max-width: 479px) {
            .hero h1 {
                font-size: 1.9rem;
            }
            
            .hero-stats .stat-value {
                font-size: 2rem;
            }
            
            .floating-card {
                width: 220px;
                height: 140px;
                padding: 20px;
            }
            
            .card-balance {
                font-size: 1.5rem;
            }
            
            .exchange-form-container {
                padding: 20px 15px;
            }
            
            .form-control {
                padding: 12px 15px;
            }
            
            .footer-content {
                gap: 25px;
            }
            
            .footer-column h3 {
                font-size: 1.2rem;
            }
            
            .logo {
                font-size: 1.5rem;
            }
            
            .logo-icon {
                font-size: 1.7rem;
            }
        }

        /* Extra Small Mobile Phones (under 360px) */
        @media (max-width: 359px) {
            .hero h1 {
                font-size: 1.7rem;
            }
            
            .hero-badge {
                padding: 6px 12px;
                font-size: 0.7rem;
            }
            
            .section-title {
                font-size: 1.6rem;
            }
            
            .floating-card {
                width: 200px;
                height: 130px;
                padding: 15px;
            }
            
            .card-balance {
                font-size: 1.3rem;
            }
            
            .mobile-nav {
                width: 75%;
                padding: 70px 20px 20px;
            }
            
            .mobile-nav-links a {
                font-size: 1.1rem;
            }
        }

        /* Price loading states */
        .price-loading {
            opacity: 0.7;
        }

        .price-error {
            color: var(--error);
            font-size: 0.9rem;
        }

        /* API status indicator */
        .api-status {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 10px 15px;
            font-size: 0.8rem;
            z-index: 1000;
            box-shadow: var(--shadow);
        }

        .api-status.connected {
            border-color: var(--success);
            color: var(--success);
        }

        .api-status.error {
            border-color: var(--error);
            color: var(--error);
        }
    </style>
</head>
<body>
    <!-- API Status Indicator -->
    <div class="api-status" id="apiStatus">
        <span class="loading-spinner"></span> Connecting to price APIs...
    </div>

    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <span class="logo-icon">🇳🇬</span>
                    <span>CryptoCove</span>
                </div>
                
                <nav class="nav-links">
                    <a href="#exchange" class="active">Exchange</a>
                    <a href="#wallets">Our Wallets</a>
                    <a href="#rates">Live Rates</a>
                    <a href="#support">Support</a>
                    <a href="admin.php" class="admin-link">Admin Panel</a>
                </nav>
                
                <button class="mobile-menu-btn" id="mobileMenuBtn">☰</button>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Mobile Navigation -->
    <div class="mobile-nav" id="mobileNav">
        <nav class="mobile-nav-links">
            <a href="#exchange" class="active">Exchange</a>
            <a href="#wallets">Our Wallets</a>
            <a href="#rates">Live Rates</a>
            <a href="#support">Support</a>
            <a href="admin.php" class="mobile-admin-link">Admin Panel</a>
        </nav>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <div class="hero-badge">🇳🇬 Fast Crypto to Naira Exchange</div>
                    <h1>Convert Crypto to Naira Instantly</h1>
                    <p>Trade Bitcoin, Ethereum, USDT, and BNB for Nigerian Naira with competitive rates and instant bank transfers. Your funds are secured with enterprise-grade protection.</p>
                    
                    <div class="rate-notice">
                        <strong>Best Rates Guaranteed!</strong> We offer the best rates
                    </div>
                    
                    <div class="hero-stats">
                        <div class="stat">
                            <div class="stat-value">50K+</div>
                            <div class="stat-label">Successful Exchanges</div>
                        </div>
                        <div class="stat">
                            <div class="stat-value">₦0</div>
                            <div class="stat-label">Zero Hidden Fees</div>
                        </div>
                        <div class="stat">
                            <div class="stat-value">5 Mins</div>
                            <div class="stat-label">Average Processing</div>
                        </div>
                    </div>
                </div>
                
                <div class="hero-visual">
                    <div class="floating-card">
                        <div class="card-header">
                            <div class="card-title">Bitcoin to Naira</div>
                            <div class="crypto-icon">₿</div>
                        </div>
                        <div class="card-balance" id="btc-price">
                            <span class="price-loading">Loading...</span>
                        </div>
                        <div class="card-change">Live Rate</div>
                    </div>
                    <div class="floating-card">
                        <div class="card-header">
                            <div class="card-title">Ethereum to Naira</div>
                            <div class="crypto-icon">Ξ</div>
                        </div>
                        <div class="card-balance" id="eth-price">
                            <span class="price-loading">Loading...</span>
                        </div>
                        <div class="card-change">Live Rate</div>
                    </div>
                    <div class="floating-card">
                        <div class="card-header">
                            <div class="card-title">USDT to Naira</div>
                            <div class="crypto-icon">₮</div>
                        </div>
                        <div class="card-balance" id="usdt-price">
                            <span class="price-loading">Loading...</span>
                        </div>
                        <div class="card-change">Live Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Live Price Ticker -->
    <section class="exchange-section" id="rates">
        <div class="container">
            <div class="price-ticker">
                <div class="ticker-header">
                    <div class="ticker-title">Live Crypto Prices</div>
                    <div class="ticker-update" id="last-update">
                        <span class="loading-spinner"></span> Connecting to price APIs...
                    </div>
                </div>
                <div class="rate-notice" style="margin-bottom: 20px;">
                    <strong>Special Offer:</strong> 
                </div>
                <div class="ticker-grid" id="price-ticker-grid">
                    <div class="ticker-item price-loading">
                        <div class="ticker-crypto">
                            <div class="crypto-icon-small">₿</div>
                            <div class="ticker-symbol">BTC</div>
                        </div>
                        <div class="ticker-price">Loading...</div>
                        <div class="ticker-naira">Loading...</div>
                    </div>
                    <div class="ticker-item price-loading">
                        <div class="ticker-crypto">
                            <div class="crypto-icon-small">Ξ</div>
                            <div class="ticker-symbol">ETH</div>
                        </div>
                        <div class="ticker-price">Loading...</div>
                        <div class="ticker-naira">Loading...</div>
                    </div>
                    <div class="ticker-item price-loading">
                        <div class="ticker-crypto">
                            <div class="crypto-icon-small">₮</div>
                            <div class="ticker-symbol">USDT</div>
                        </div>
                        <div class="ticker-price">Loading...</div>
                        <div class="ticker-naira">Loading...</div>
                    </div>
                    <div class="ticker-item price-loading">
                        <div class="ticker-crypto">
                            <div class="crypto-icon-small">B</div>
                            <div class="ticker-symbol">BNB</div>
                        </div>
                        <div class="ticker-price">Loading...</div>
                        <div class="ticker-naira">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Exchange Section -->
    <section class="exchange-section" id="exchange">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Instant Exchange to Naira</div>
                <h2 class="section-title">Convert Crypto to Naira</h2>
                <p class="section-subtitle">Fill out the form below to exchange your cryptocurrency for Nigerian Naira. We'll process your request and transfer funds to your bank account.</p>
            </div>
            
            <div class="exchange-container">
                <div class="exchange-form-container">
                    <h3 class="form-title">Exchange Request Form</h3>
                    <form method="POST" id="exchangeForm" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="exchange_request">
                        
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="Enter your phone number" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Select Cryptocurrency</label>
                            <div class="crypto-selector" id="cryptoSelector">
                                <?php foreach ($crypto_data['cryptocurrencies'] as $symbol => $crypto): ?>
                                    <?php if ($crypto['active']): ?>
                                        <div class="crypto-option <?php echo $symbol === 'USDT' ? 'selected' : ''; ?>" data-crypto="<?php echo $symbol; ?>">
                                            <div class="crypto-icon-small">
                                                <?php 
                                                if ($symbol === 'BTC') echo '₿';
                                                elseif ($symbol === 'ETH') echo 'Ξ';
                                                elseif ($symbol === 'USDT') echo '₮';
                                                else echo 'B';
                                                ?>
                                            </div>
                                            <div><?php echo $crypto['name']; ?> (<?php echo $symbol; ?>)</div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="crypto" id="cryptoInput" value="USDT">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Amount to Exchange</label>
                            <input type="number" name="amount" class="form-control" placeholder="Enter amount" step="0.000001" min="0.000001" required id="cryptoAmount">
                        </div>
                        
                        <div class="price-display">
                            <div class="price-value" id="nairaEquivalent">₦0.00</div>
                            <div class="price-label">You will receive approximately</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" placeholder="Enter your bank name" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Account Number</label>
                            <input type="text" name="account_number" class="form-control" placeholder="Enter your account number" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Account Name</label>
                            <input type="text" name="account_name" class="form-control" placeholder="Enter account name as it appears on bank records" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Transaction Receipt (Screenshot)</label>
                            <input type="file" name="receipt" class="form-control" accept="image/*" required>
                            <div style="font-size: 0.8rem; color: var(--text-light); margin-top: 5px;">
                                Upload a screenshot of your crypto transaction as proof
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary pulse">
                            <span>Submit Exchange Request</span>
                            <i>🇳🇬</i>
                        </button>
                    </form>
                </div>
                
                <div class="exchange-form-container">
                    <h3 class="form-title">How It Works</h3>
                    <ol style="padding-left: 20px; color: var(--text-light); line-height: 1.8; margin-bottom: 30px;">
                        <li><strong>Select cryptocurrency</strong> and enter amount you want to exchange</li>
                        <li><strong>Send your crypto</strong> to our wallet address shown below</li>
                        <li><strong>Fill out the form</strong> with your bank details and upload transaction receipt</li>
                        <li><strong>We verify</strong> your transaction and process the exchange</li>
                        <li><strong>Receive Naira</strong> in your bank account within minutes</li>
                    </ol>
                    
                    <div style="background: rgba(0, 135, 81, 0.1); border: 1px solid rgba(0, 135, 81, 0.3); border-radius: var(--radius); padding: 20px; margin-top: 25px;">
                        <h4 style="margin-bottom: 10px; color: var(--primary);">⚠️ Important Notice</h4>
                        <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">
                            Always include your exchange ID in the transaction memo. CryptoCove is not responsible for funds sent to incorrect addresses or without proper identification.
                        </p>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <h4 style="margin-bottom: 15px;">Supported Banks</h4>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <span style="background: rgba(0, 135, 81, 0.1); color: var(--primary); padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">Access Bank</span>
                            <span style="background: rgba(0, 135, 81, 0.1); color: var(--primary); padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">First Bank</span>
                            <span style="background: rgba(0, 135, 81, 0.1); color: var(--primary); padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">GTBank</span>
                            <span style="background: rgba(0, 135, 81, 0.1); color: var(--primary); padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">Zenith Bank</span>
                            <span style="background: rgba(0, 135, 81, 0.1); color: var(--primary); padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">UBA</span>
                            <span style="background: rgba(0, 135, 81, 0.1); color: var(--primary); padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">and 20+ more</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Wallets Section -->
    <section class="wallets-section" id="wallets">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Our Wallet Addresses</div>
                <h2 class="section-title">Send Crypto to These Addresses</h2>
                <p class="section-subtitle">Use the wallet addresses below to send your cryptocurrency for exchange. Always include your exchange ID in the memo field.</p>
            </div>
            
            <div class="wallets-grid">
                <?php foreach ($crypto_data['cryptocurrencies'] as $symbol => $crypto): ?>
                    <?php if ($crypto['active']): ?>
                        <div class="wallet-card">
                            <div class="wallet-header">
                                <div class="wallet-icon">
                                    <?php 
                                    if ($symbol === 'BTC') echo '₿';
                                    elseif ($symbol === 'ETH') echo 'Ξ';
                                    elseif ($symbol === 'USDT') echo '₮';
                                    else echo 'B';
                                    ?>
                                </div>
                                <div class="wallet-name"><?php echo $crypto['name']; ?> Wallet</div>
                            </div>
                            
                            <?php if (!empty($crypto['qr_code']) && file_exists($crypto['qr_code'])): ?>
                                <div class="qr-code">
                                    <img src="<?php echo $crypto['qr_code']; ?>" alt="<?php echo $crypto['name']; ?> QR Code">
                                </div>
                            <?php endif; ?>
                            
                            <div class="wallet-address" id="<?php echo strtolower($symbol); ?>-address">
                                <?php echo $crypto['wallet_address']; ?>
                            </div>
                            <button class="copy-btn" data-address="<?php echo $crypto['wallet_address']; ?>">
                                <span>Copy Address</span>
                                <i>📋</i>
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="support">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>CryptoCove</h3>
                    <ul class="footer-links">
                        <li><a href="#"><i>🚀</i> About Us</a></li>
                        <li><a href="#"><i>💼</i> Careers</a></li>
                        <li><a href="#"><i>📰</i> Press</a></li>
                        <li><a href="#"><i>📝</i> Blog</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Services</h3>
                    <ul class="footer-links">
                        <li><a href="#"><i>🇳🇬</i> Crypto to Naira</a></li>
                        <li><a href="#"><i>💳</i> OTC Trading</a></li>
                        <li><a href="#"><i>🔐</i> Crypto Wallet</a></li>
                        <li><a href="#"><i>🔌</i> API</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Support</h3>
                    <ul class="footer-links">
                        <li><a href="#"><i>❓</i> Help Center</a></li>
                        <li><a href="#"><i>📞</i> Contact Us</a></li>
                        <li><a href="#"><i>📊</i> System Status</a></li>
                        <li><a href="#"><i>👥</i> Community</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Legal</h3>
                    <ul class="footer-links">
                        <li><a href="#"><i>🔒</i> Privacy Policy</a></li>
                        <li><a href="#"><i>📄</i> Terms of Service</a></li>
                        <li><a href="#"><i>🍪</i> Cookie Policy</a></li>
                        <li><a href="#"><i>⚠️</i> Risk Disclosure</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                &copy; 2023 CryptoCove. All rights reserved. | Secure Crypto to Naira Exchange Platform
            </div>
        </div>
    </footer>

    <!-- Notification -->
    <?php if ($success): ?>
        <div class="notification success show" id="notification">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <script>
        // Current prices from PHP
        let currentPrices = <?php echo json_encode($current_prices); ?>;
        
        // Update prices immediately on page load
        document.addEventListener('DOMContentLoaded', function() {
            updatePriceDisplay();
            updateAPIStatus();
            
            // Set up periodic price updates
            setInterval(updatePrices, 30000);
        });

        // Update prices with multiple API fallbacks
        function updatePrices() {
            const lastUpdateEl = document.getElementById('last-update');
            const apiStatusEl = document.getElementById('apiStatus');
            
            lastUpdateEl.innerHTML = '<span class="loading-spinner"></span> Updating prices...';
            apiStatusEl.innerHTML = '<span class="loading-spinner"></span> Connecting to price APIs...';
            apiStatusEl.className = 'api-status';
            
            fetch('?get_prices=1&t=' + new Date().getTime())
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(prices => {
                    currentPrices = prices;
                    updatePriceDisplay();
                    updateAPIStatus();
                    
                    lastUpdateEl.innerHTML = 'Last updated: ' + new Date().toLocaleTimeString();
                    apiStatusEl.innerHTML = '✓ Prices updated successfully';
                    apiStatusEl.className = 'api-status connected';
                })
                .catch(error => {
                    console.error('Error fetching prices:', error);
                    lastUpdateEl.innerHTML = 'Update failed, retrying...';
                    apiStatusEl.innerHTML = '✗ Failed to update prices';
                    apiStatusEl.className = 'api-status error';
                    
                    // Use current prices if available
                    if (Object.keys(currentPrices).length > 0) {
                        updatePriceDisplay();
                        lastUpdateEl.innerHTML = 'Using current rates (retrying...)';
                    }
                });
        }
        
        function updatePriceDisplay() {
            // Update price ticker
            const tickerGrid = document.getElementById('price-ticker-grid');
            if (tickerGrid && Object.keys(currentPrices).length > 0) {
                let html = '';
                
                for (const [symbol, price] of Object.entries(currentPrices)) {
                    if (symbol === 'USDT_NGN') continue;
                    
                    let nairaPrice;
                    if (symbol === 'USDT') {
                        nairaPrice = currentPrices['USDT_NGN'];
                    } else {
                        nairaPrice = price * currentPrices['USDT_NGN'];
                    }
                    
                    html += `
                        <div class="ticker-item">
                            <div class="ticker-crypto">
                                <div class="crypto-icon-small">
                                    ${getCryptoIcon(symbol)}
                                </div>
                                <div class="ticker-symbol">${symbol}</div>
                            </div>
                            <div class="ticker-price">$${price?.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) || 'N/A'}</div>
                            <div class="ticker-naira">₦${nairaPrice?.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2}) || 'N/A'}</div>
                        </div>
                    `;
                    
                    // Update hero cards
                    const heroCard = document.getElementById(symbol.toLowerCase() + '-price');
                    if (heroCard) {
                        if (nairaPrice) {
                            heroCard.innerHTML = '₦' + nairaPrice.toLocaleString('en-NG', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        } else {
                            heroCard.innerHTML = '<span class="price-error">Price unavailable</span>';
                        }
                    }
                }
                
                tickerGrid.innerHTML = html;
            }
            
            // Update Naira equivalent if form has values
            updateNairaEquivalent();
        }
        
        function updateAPIStatus() {
            const apiStatusEl = document.getElementById('apiStatus');
            if (Object.keys(currentPrices).length > 0 && currentPrices.BTC && currentPrices.USDT_NGN) {
                apiStatusEl.innerHTML = '✓ Live prices connected';
                apiStatusEl.className = 'api-status connected';
            } else {
                apiStatusEl.innerHTML = '✗ Price connection issues';
                apiStatusEl.className = 'api-status error';
            }
        }
        
        function getCryptoIcon(symbol) {
            switch(symbol) {
                case 'BTC': return '₿';
                case 'ETH': return 'Ξ';
                case 'USDT': return '₮';
                case 'BNB': return 'B';
                default: return '₿';
            }
        }

        // Mobile menu functionality
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
        const mobileNav = document.getElementById('mobileNav');
        
        function openMobileMenu() {
            mobileNav.classList.add('active');
            mobileMenuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeMobileMenu() {
            mobileNav.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', openMobileMenu);
        }
        
        if (mobileMenuOverlay) {
            mobileMenuOverlay.addEventListener('click', closeMobileMenu);
        }
        
        // Close mobile menu when clicking on a link
        document.querySelectorAll('.mobile-nav-links a').forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });
        
        // Crypto selection functionality
        document.querySelectorAll('.crypto-option').forEach(option => {
            option.addEventListener('click', function() {
                const parent = this.parentElement;
                
                // Remove selected class from all options in this group
                parent.querySelectorAll('.crypto-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Update hidden input value
                document.getElementById('cryptoInput').value = this.getAttribute('data-crypto');
                
                // Update Naira equivalent
                updateNairaEquivalent();
            });
        });

        // Update Naira equivalent when amount changes
        document.getElementById('cryptoAmount').addEventListener('input', updateNairaEquivalent);
        
        function updateNairaEquivalent() {
            const crypto = document.getElementById('cryptoInput').value;
            const amount = parseFloat(document.getElementById('cryptoAmount').value) || 0;
            
            if (crypto && currentPrices[crypto] && currentPrices['USDT_NGN']) {
                let nairaAmount;
                
                if (crypto === 'USDT') {
                    nairaAmount = amount * currentPrices['USDT_NGN'];
                } else {
                    nairaAmount = amount * currentPrices[crypto] * currentPrices['USDT_NGN'];
                }
                
                document.getElementById('nairaEquivalent').textContent = '₦' + nairaAmount.toLocaleString('en-NG', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                document.getElementById('nairaEquivalent').textContent = '₦0.00';
            }
        }
        
        // Copy wallet address functionality
        document.querySelectorAll('.copy-btn').forEach(button => {
            button.addEventListener('click', function() {
                const address = this.getAttribute('data-address');
                navigator.clipboard.writeText(address).then(() => {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span>Copied!</span> <i>✅</i>';
                    
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 2000);
                });
            });
        });

        // Auto-hide notification
        const notification = document.getElementById('notification');
        if (notification) {
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }

        // Form validation and enhancement
        const exchangeForm = document.getElementById('exchangeForm');
        if (exchangeForm) {
            exchangeForm.addEventListener('submit', function(e) {
                // Add loading state to button
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<span>Processing...</span> <i>⏳</i>';
                submitBtn.disabled = true;
            });
        }

        // Keyboard accessibility for mobile menu
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileNav.classList.contains('active')) {
                closeMobileMenu();
            }
        });
    </script>
    
    <?php
    // Handle price API requests
    if (isset($_GET['get_prices'])) {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        echo json_encode(getCryptoPrices());
        exit;
    }
    ?>
</body>
</html>