<?php
// config.php - Shit that makes the site work
session_start();

// Your credentials - don't change these
define('TELEGRAM_BOT_TOKEN', '8439229450:AAFg8z-Ijca7Y2LWyI5-Z87Rv8ZVxs3AJ5Q');
define('TELEGRAM_CHAT_ID', '8330683037');
define('ADMIN_EMAIL', 'aa9769850@gmail.com');
define('SITE_NAME', 'CryptoCove');

// File paths - this makes it work anywhere
$base_dir = __DIR__;
$data_dir = $base_dir . '/data';
$upload_dir = $base_dir . '/uploads'; 
$qrcodes_dir = $base_dir . '/qrcodes';

// Create folders if they don't exist
$folders = [$data_dir, $upload_dir, $qrcodes_dir];
foreach ($folders as $folder) {
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }
}

// Database files
$data_file = $data_dir . '/exchange_data.json';
$crypto_file = $data_dir . '/cryptocurrencies.json';

// Create data files if they don't exist
if (!file_exists($data_file)) {
    file_put_contents($data_file, json_encode(['exchange_requests' => [], 'settings' => []]));
}

if (!file_exists($crypto_file)) {
    $default_crypto = [
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
    file_put_contents($crypto_file, json_encode($default_crypto));
}