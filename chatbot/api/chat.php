<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(['error' => 'Requête invalide']));
}

$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';

if (empty($message)) {
    exit(json_encode(['error' => 'Aucun message fourni']));
}

// Réponses simples (vous pouvez améliorer cela plus tard)
$responses = [
    'hello' => 'Bonjour ! Bienvenue au Centre Médical DOCTO LINK. Comment puis-je vous aider ?',
    'appointment' => 'Pour prendre rendez-vous, veuillez vous connecter et aller à la section des rendez-vous.',
    'doctor' => 'Nous avons des médecins qualifiés disponibles. Veuillez consulter notre liste de médecins.',
    'help' => 'Je peux vous aider avec les rendez-vous, les informations sur les médecins et les questions générales.',
    'default' => 'Merci pour votre message. Pour des conseils médicaux spécifiques, veuillez consulter nos médecins.'
];

// Correspondance simple par mots-clés
$response = $responses['default'];
$message_lower = strtolower($message);

foreach ($responses as $keyword => $reply) {
    if (strpos($message_lower, $keyword) !== false) {
        $response = $reply;
        break;
    }
}

echo json_encode([
    'success' => true,
    'response' => $response
]);
?>