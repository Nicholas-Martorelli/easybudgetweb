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
    
    // Verifica se è presente l'ID utente come parametro GET
    if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID utente mancante o non valido"]);
        exit;
    }
    
    $userId = (int)$_GET['user_id'];
    
    // Prima verifichiamo che l'utente esista
    $checkUser = $conn->prepare("SELECT id FROM utenti WHERE id = ?");
    $checkUser->bind_param("i", $userId);
    $checkUser->execute();
    $userResult = $checkUser->get_result();
    
    if ($userResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Utente non trovato"]);
        exit;
    }

    // Parametri opzionali per filtrare per mese/anno
    $year = isset($_GET['year']) ? (int)$_GET['year'] : null;
    $month = isset($_GET['month']) ? (int)$_GET['month'] : null;

    try {
        // Costruisci la query per le spese
        $whereClause = "WHERE user_id = ?";
        $params = [$userId];
        $paramTypes = "i";

        if ($year !== null && $month !== null) {
            $whereClause .= " AND YEAR(expense_date) = ? AND MONTH(expense_date) = ?";
            $params[] = $year;
            $params[] = $month;
            $paramTypes .= "ii";
        } elseif ($year !== null) {
            $whereClause .= " AND YEAR(expense_date) = ?";
            $params[] = $year;
            $paramTypes .= "i";
        }

        // Recupera le spese
        $stmt = $conn->prepare("
            SELECT id, amount, description, expense_date
            FROM expenses
            $whereClause
            ORDER BY expense_date DESC, created_at DESC
        ");
        
        $stmt->bind_param($paramTypes, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $expenses = [];
        while ($row = $result->fetch_assoc()) {
            $expenses[] = [
                'id' => (int)$row['id'],
                'amount' => (float)$row['amount'],
                'description' => $row['description'],
                'expense_date' => $row['expense_date']
            ];
        }

        // Calcola il totale per il periodo richiesto
        $total = array_sum(array_column($expenses, 'amount'));

        echo json_encode([
            "success" => true,
            "data" => [
                "expenses" => $expenses,
                "total" => $total
            ]
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Errore nel caricamento: " . $e->getMessage()]);
    }
}

function handlePostRequest() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validazione dei dati
    if (!isset($input['user_id']) || !is_numeric($input['user_id'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID utente mancante o non valido"]);
        exit;
    }
    
    // Verifica che l'utente esista
    $userId = (int)$input['user_id'];
    $checkUser = $conn->prepare("SELECT id FROM utenti WHERE id = ?");
    $checkUser->bind_param("i", $userId);
    $checkUser->execute();
    $userResult = $checkUser->get_result();
    
    if ($userResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Utente non trovato"]);
        exit;
    }

    if (!isset($input['amount']) || !is_numeric($input['amount']) || $input['amount'] <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Importo mancante o non valido"]);
        exit;
    }

    if (!isset($input['description']) || empty(trim($input['description']))) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Descrizione mancante"]);
        exit;
    }

    if (!isset($input['expense_date']) || empty($input['expense_date'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Data spesa mancante"]);
        exit;
    }

    $amount = (float)$input['amount'];
    $description = trim($input['description']);
    $expenseDate = $input['expense_date'];

    try {
        // Inserisci la spesa
        $stmt = $conn->prepare("
            INSERT INTO expenses (user_id, amount, description, expense_date, created_at, updated_at) 
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param("idss", $userId, $amount, $description, $expenseDate);
        
        if ($stmt->execute()) {
            $expenseId = $conn->insert_id;
            
            echo json_encode([
                "success" => true,
                "message" => "Spesa aggiunta con successo",
                "expense_id" => $expenseId
            ]);
        } else {
            throw new Exception("Errore nell'inserimento della spesa");
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Errore nel salvataggio: " . $e->getMessage()]);
    }
}

function handlePutRequest() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validazione dei dati
    if (!isset($input['user_id']) || !is_numeric($input['user_id'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID utente mancante o non valido"]);
        exit;
    }
    
    if (!isset($input['id']) || !is_numeric($input['id'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID spesa mancante o non valido"]);
        exit;
    }

    if (!isset($input['amount']) || !is_numeric($input['amount']) || $input['amount'] <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Importo mancante o non valido"]);
        exit;
    }

    if (!isset($input['description']) || empty(trim($input['description']))) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Descrizione mancante"]);
        exit;
    }

    if (!isset($input['expense_date']) || empty($input['expense_date'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Data spesa mancante"]);
        exit;
    }

    $userId = (int)$input['user_id'];
    $expenseId = (int)$input['id'];
    $amount = (float)$input['amount'];
    $description = trim($input['description']);
    $expenseDate = $input['expense_date'];

    // Verifica che l'utente esista
    $checkUser = $conn->prepare("SELECT id FROM utenti WHERE id = ?");
    $checkUser->bind_param("i", $userId);
    $checkUser->execute();
    $userResult = $checkUser->get_result();
    
    if ($userResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Utente non trovato"]);
        exit;
    }

    // Verifica che la spesa appartenga all'utente
    $checkStmt = $conn->prepare("SELECT id FROM expenses WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $expenseId, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Spesa non trovata o non autorizzata"]);
        exit;
    }

    try {
        // Aggiorna la spesa
        $stmt = $conn->prepare("
            UPDATE expenses 
            SET amount = ?, description = ?, expense_date = ?, updated_at = NOW() 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("dssii", $amount, $description, $expenseDate, $expenseId, $userId);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    "success" => true,
                    "message" => "Spesa aggiornata con successo"
                ]);
            } else {
                echo json_encode([
                    "success" => true,
                    "message" => "Nessuna modifica necessaria"
                ]);
            }
        } else {
            throw new Exception("Errore nell'aggiornamento della spesa");
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Errore nell'aggiornamento: " . $e->getMessage()]);
    }
}

function handleDeleteRequest() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validazione dei dati
    if (!isset($input['user_id']) || !is_numeric($input['user_id'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID utente mancante o non valido"]);
        exit;
    }
    
    if (!isset($input['id']) || !is_numeric($input['id'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID spesa mancante o non valido"]);
        exit;
    }

    $userId = (int)$input['user_id'];
    $expenseId = (int)$input['id'];

    // Verifica che l'utente esista
    $checkUser = $conn->prepare("SELECT id FROM utenti WHERE id = ?");
    $checkUser->bind_param("i", $userId);
    $checkUser->execute();
    $userResult = $checkUser->get_result();
    
    if ($userResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Utente non trovato"]);
        exit;
    }

    // Verifica che la spesa appartenga all'utente
    $checkStmt = $conn->prepare("SELECT id FROM expenses WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $expenseId, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Spesa non trovata o non autorizzata"]);
        exit;
    }

    try {
        $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $expenseId, $userId);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    "success" => true,
                    "message" => "Spesa eliminata con successo"
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["success" => false, "message" => "Nessuna spesa trovata per l'eliminazione"]);
            }
        } else {
            throw new Exception("Errore nell'eliminazione della spesa");
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Errore nell'eliminazione: " . $e->getMessage()]);
    }
}