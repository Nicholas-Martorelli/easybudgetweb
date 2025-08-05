<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include 'config.php';

$conn = null;
$stmt = null;

try {
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed");
    }
    
    // Ottieni user_id
    $userId = $_GET['user_id'] ?? null;
    if (empty($userId) || !is_numeric($userId)) {
        throw new Exception("User ID mancante o non valido");
    }
    
    // Query per contare i salvadanai dalla tabella savings
    $sql = "SELECT COUNT(*) as savings_count FROM savings WHERE user_id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $userId);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    // Se non ci sono dati, restituisci valori di default
    if (!$data) {
        $data = [
            'savings_count' => 0
        ];
    }
    
    // Verifica se ci sono davvero dati per l'utente
    $hasData = ($data['savings_count'] > 0);
    
    echo json_encode([
        "success" => true,
        "has_data" => $hasData,
        "data" => [
            "savings_count" => (int)($data['savings_count'] ?? 0)
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Server error: " . $e->getMessage(),
        "has_data" => false,
        "data" => null
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>