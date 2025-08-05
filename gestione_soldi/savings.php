<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

include 'config.php';

// Leggi il metodo della richiesta
$method = $_SERVER['REQUEST_METHOD'];

// Gestisci le diverse richieste
switch ($method) {
    case 'GET':
        handleGetRequest();
        break;
    case 'POST':
        handlePostRequest();
        break;
    case 'PUT':
        handlePutRequest();
        break;
    case 'DELETE':
        handleDeleteRequest();
        break;
    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Metodo non consentito"]);
        break;
}

function handleGetRequest() {
    global $conn;
    
    $userId = $_GET['user_id'] ?? null;
    
    if (empty($userId)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "User ID mancante"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM savings WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $savings = [];
    while ($row = $result->fetch_assoc()) {
        $savings[] = $row;
    }

    echo json_encode([
        "success" => true,
        "data" => $savings
    ]);
}

function handlePostRequest() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validazione dei dati - campi richiesti per la creazione
    $required = ['user_id', 'name', 'target_amount', 'color', 'icon_name'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || (is_string($input[$field]) && trim($input[$field]) === '')) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Campo $field mancante o vuoto"]);
            exit;
        }
    }

    // Validazione dei tipi
    if (!is_numeric($input['target_amount']) || $input['target_amount'] <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "L'importo obiettivo deve essere un numero positivo"]);
        exit;
    }

    // Validazione del colore (deve essere un numero intero)
    if (!is_numeric($input['color'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Il colore deve essere un valore numerico"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO savings (user_id, name, target_amount, current_amount, color, icon_name, created_at) VALUES (?, ?, ?, 0, ?, ?, NOW())");
    $stmt->bind_param("isdis", 
        $input['user_id'],
        $input['name'],
        $input['target_amount'],
        $input['color'],
        $input['icon_name']
    );

    if ($stmt->execute()) {
        $newId = $stmt->insert_id;
        
        // Recupera il record appena creato
        $selectStmt = $conn->prepare("SELECT * FROM savings WHERE id = ?");
        $selectStmt->bind_param("i", $newId);
        $selectStmt->execute();
        $result = $selectStmt->get_result();
        $newRecord = $result->fetch_assoc();
        
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Salvadanaio creato con successo",
            "data" => $newRecord
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Errore nel salvataggio: " . $conn->error]);
    }
}

function handlePutRequest() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validazione dei dati - campi richiesti per l'aggiornamento
    $required = ['id', 'user_id', 'name', 'target_amount', 'current_amount', 'color', 'icon_name'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || (is_string($input[$field]) && trim($input[$field]) === '')) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Campo $field mancante o vuoto"]);
            exit;
        }
    }

    // Validazione dei tipi
    if (!is_numeric($input['target_amount']) || $input['target_amount'] <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "L'importo obiettivo deve essere un numero positivo"]);
        exit;
    }

    if (!is_numeric($input['current_amount']) || $input['current_amount'] < 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "L'importo attuale deve essere un numero non negativo"]);
        exit;
    }

    // Validazione del colore (deve essere un numero intero)
    if (!is_numeric($input['color'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Il colore deve essere un valore numerico"]);
        exit;
    }

    // Verifica che il salvadanaio appartenga all'utente
    $checkStmt = $conn->prepare("SELECT id FROM savings WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $input['id'], $input['user_id']);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Salvadanaio non trovato o non autorizzato"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE savings SET name = ?, target_amount = ?, current_amount = ?, color = ?, icon_name = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sddisii", 
        $input['name'],
        $input['target_amount'],
        $input['current_amount'],
        $input['color'],
        $input['icon_name'],
        $input['id'],
        $input['user_id']
    );

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Recupera il record aggiornato
            $selectStmt = $conn->prepare("SELECT * FROM savings WHERE id = ?");
            $selectStmt->bind_param("i", $input['id']);
            $selectStmt->execute();
            $result = $selectStmt->get_result();
            $updatedRecord = $result->fetch_assoc();
            
            echo json_encode([
                "success" => true,
                "message" => "Salvadanaio aggiornato con successo",
                "data" => $updatedRecord
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Nessun salvadanaio trovato per l'aggiornamento"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Errore nell'aggiornamento: " . $conn->error]);
    }
}

function handleDeleteRequest() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validazione dei dati
    if (!isset($input['id']) || !isset($input['user_id'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID e User ID sono richiesti"]);
        exit;
    }

    // Verifica che il salvadanaio appartenga all'utente
    $checkStmt = $conn->prepare("SELECT id FROM savings WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $input['id'], $input['user_id']);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Salvadanaio non trovato o non autorizzato"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM savings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $input['id'], $input['user_id']);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Salvadanaio eliminato con successo"
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Nessun salvadanaio trovato per l'eliminazione"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Errore nell'eliminazione: " . $conn->error]);
    }
}
?>