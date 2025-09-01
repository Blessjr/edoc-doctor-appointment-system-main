<?php
class MedicalChatBot {
    private $db;
    private $userRole;
    private $userId;
    
    public function __construct($connection, $userRole = 'guest', $userId = null) {
        $this->db = $connection;
        $this->userRole = $userRole;
        $this->userId = $userId;
    }
    
    public function processMessage($message) {
        // Determine system prompt based on user role
        $systemPrompt = $this->getSystemPromptByRole();
        
        // Get user context if logged in
        $userContext = $this->getUserContext();
        
        // Call AI API with medical context
        $response = $this->callAIAPI($message, $systemPrompt, $userContext);
        
        // Log conversation for medical compliance
        $this->logConversation($message, $response);
        
        return $response;
    }
    
    private function getSystemPromptByRole() {
        $prompts = [
            'doctor' => 'You are a medical AI assistant helping doctors with patient care, diagnosis support, and medical information. Always remind users to verify information and consult medical guidelines.',
            'nurse' => 'You are a medical AI assistant helping nurses with patient care, medication information, and nursing procedures. Always emphasize patient safety.',
            'patient' => 'You are a helpful medical AI assistant for patients. Provide general health information but always advise consulting healthcare professionals for medical advice. Never provide specific diagnoses.',
            'admin' => 'You are an AI assistant helping with hospital administration, scheduling, and general inquiries.',
            'guest' => 'You are a general AI assistant for the medical center. Provide basic information about services and direct users to appropriate contacts.'
        ];
        
        return $prompts[$this->userRole] ?? $prompts['guest'];
    }
    
    private function getUserContext() {
        if (!$this->userId) return '';
        
        // Get relevant user information based on role
        switch ($this->userRole) {
            case 'doctor':
                $stmt = $this->db->prepare("SELECT docname, specialties FROM doctor WHERE docid = ?");
                break;
            case 'patient':
                $stmt = $this->db->prepare("SELECT pname FROM patient WHERE pid = ?");
                break;
            case 'admin':
                $stmt = $this->db->prepare("SELECT aemail FROM admin WHERE aid = ?");
                break;
            default:
                return '';
        }
        
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result ? "User context: " . json_encode($result) : '';
    }
    
    private function callAIAPI($message, $systemPrompt, $userContext) {
        $apiKey = 'your-openai-api-key'; // Move to config
        $url = 'https://api.openai.com/v1/chat/completions';
        
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt . ' ' . $userContext],
            ['role' => 'user', 'content' => $message]
        ];
        
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'max_tokens' => 200,
            'temperature' => 0.3 // Lower temperature for medical accuracy
        ];
        
        $options = [
            'http' => [
                'header' => [
                    "Content-Type: application/json",
                    "Authorization: Bearer $apiKey"
                ],
                'method' => 'POST',
                'content' => json_encode($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        if ($response === FALSE) {
            return 'Sorry, I am currently unavailable. Please contact support.';
        }
        
        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? 'No response available.';
    }
    
    private function logConversation($message, $response) {
        $stmt = $this->db->prepare("INSERT INTO chat_logs (user_id, user_role, message, response, timestamp) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isss", $this->userId, $this->userRole, $message, $response);
        $stmt->execute();
    }
}
?>