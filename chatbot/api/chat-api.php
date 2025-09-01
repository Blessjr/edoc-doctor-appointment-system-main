<?php
// Enable error reporting for debugging (REMOVE IN PRODUCTION)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Start PHP session if not already started
header('Content-Type: application/json'); // Set response header to JSON
header('Access-Control-Allow-Origin: *'); // Allow requests from any domain (for development)
header('Access-Control-Allow-Methods: POST'); // Only allow POST requests
header('Access-Control-Allow-Headers: Content-Type'); // Allow Content-Type header

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit(json_encode(['error' => 'Method not allowed. Only POST requests are accepted.']));
}

// Decode the JSON input from the frontend
$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? ''; // Get the user's message

// Validate if a message was provided
if (empty($message)) {
    exit(json_encode(['error' => 'No message provided.']));
}

// --- IMPORTANT: REPLACE 'YOUR_OPENAI_API_KEY_HERE' WITH YOUR ACTUAL KEY ---
$openai_api_key = 'YOUR_OPENAI_API_KEY_HERE'; 
// --- END IMPORTANT ---

$openai_url = 'https://api.openai.com/v1/chat/completions';

// Define a simple, fixed system prompt for the AI
$systemPrompt = "You are DOCTO LINK AI, a helpful medical assistant for the DOCTO LINK Medical Center. You can answer general medical questions, provide information about appointments, services, and navigate the DOCTO LINK system. Always advise users to consult healthcare professionals for specific medical advice. Be concise and helpful.";

// Prepare the data payload for the OpenAI API request
$data = [
    'model' => 'gpt-3.5-turbo', // You can try 'gpt-4o' if you have access and prefer a more powerful model
    'messages' => [
        [
            'role' => 'system',
            'content' => $systemPrompt
        ],
        [
            'role' => 'user',
            'content' => $message
        ]
    ],
    'max_tokens' => 300, // Limit the length of the AI's response
    'temperature' => 0.7 // Controls randomness: 0.0 for deterministic, 1.0 for creative
];

// Set up stream context for the HTTP POST request
$options = [
    'http' => [
        'header' => [
            "Content-Type: application/json",
            "Authorization: Bearer $openai_api_key" // Your API key goes here
        ],
        'method' => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true // Essential to read error responses from the API
    ]
];

$context = stream_context_create($options);

// Make the actual call to the OpenAI API
$response = @file_get_contents($openai_url, false, $context); // Using @ to suppress warnings for now

// Handle potential network or server connection issues
if ($response === FALSE) {
    exit(json_encode([
        'success' => false,
        'error' => 'Failed to connect to AI service. Please check server network.'
    ]));
}

$result = json_decode($response, true); // Decode the JSON response from OpenAI

// Check for errors returned by the OpenAI API (e.g., invalid key, rate limits)
if (isset($result['error'])) {
    $errorMessage = $result['error']['message'] ?? 'Unknown OpenAI API error';
    // For debugging, you can uncomment the line below to log the error to your server's PHP error log
    // error_log("OpenAI API Error: " . $errorMessage);
    exit(json_encode([
        'success' => false,
        'error' => "AI Service Error: " . $errorMessage
    ]));
}

// Extract the AI's response content
$aiResponse = $result['choices'][0]['message']['content'] ?? 'I apologize, but I could not generate a response. Please try again.';

// Send the successful AI response back to the frontend
echo json_encode([
    'success' => true,
    'response' => $aiResponse
]);
?>
