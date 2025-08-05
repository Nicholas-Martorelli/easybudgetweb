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
    
    if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID utente mancante o non valido"]);
        exit;
    }
    
    $userId = (int)$_GET['user_id'];
    $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
    $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
    $monthFormatted = sprintf('%04d-%02d', $year, $month);

    try {
        // Recupera lo stipendio mensile per il mese specificato
        $stmt = $conn->prepare("SELECT monthly_salary FROM salary_split_preferences WHERE user_id = ? AND month = ?");
        $stmt->bind_param("is", $userId, $monthFormatted);
        $stmt->execute();
        $result = $stmt->get_result();
        $salaryData = $result->fetch_assoc();
        
        $monthlySalary = $salaryData ? (float)$salaryData['monthly_salary'] : 0.0;

        // Recupera le categorie di budget per il mese specificato
        $stmt = $conn->prepare("SELECT id, name, amount, is_percentage FROM budget_categories WHERE user_id = ? AND month = ? ORDER BY created_at ASC");
        $stmt->bind_param("is", $userId, $monthFormatted);
        $stmt->execute();
        $result = $stmt->get_result();

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'amount' => (float)$row['amount'],
                'is_percentage' => (int)$row['is_percentage']
            ];
        }

        echo json_encode([
            "success" => true,
            "data" => [
                "monthly_salary" => $monthlySalary,
                "categories" => $categories,
                "month" => $monthFormatted
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
    
    if (!isset($input['user_id']) || !is_numeric($input['user_id'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID utente mancante o non valido"]);
        exit;
    }
    
    if (!isset($input['monthly_salary']) || !is_numeric($input['monthly_salary'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Stipendio mensile mancante o non valido"]);
        exit;
    }

    if (!isset($input['categories']) || !is_array($input['categories'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Categorie mancanti o non valide"]);
        exit;
    }

    $userId = (int)$input['user_id'];
    $monthlySalary = (float)$input['monthly_salary'];
    $categories = $input['categories'];
    $year = isset($input['year']) ? (int)$input['year'] : (int)date('Y');
    $month = isset($input['month']) ? (int)$input['month'] : (int)date('m');
    
    // Formatta correttamente il mese come 'YYYY-MM'
    $monthFormatted = sprintf('%04d-%02d', $year, $month);

    $conn->begin_transaction();

    try {
        // Salva/aggiorna lo stipendio mensile per il mese specificato
        $stmt = $conn->prepare("
            INSERT INTO salary_split_preferences (user_id, monthly_salary, month, created_at, updated_at) 
            VALUES (?, ?, ?, NOW(), NOW()) 
            ON DUPLICATE KEY UPDATE 
            monthly_salary = VALUES(monthly_salary), 
            updated_at = NOW()
        ");
        $stmt->bind_param("ids", $userId, $monthlySalary, $monthFormatted);
        $stmt->execute();

        // Elimina le categorie esistenti per questo utente e mese
        $stmt = $conn->prepare("DELETE FROM budget_categories WHERE user_id = ? AND month = ?");
        $stmt->bind_param("is", $userId, $monthFormatted);
        $stmt->execute();

        // Inserisci le nuove categorie con il mese
        if (!empty($categories)) {
            $stmt = $conn->prepare("
                INSERT INTO budget_categories (user_id, name, amount, is_percentage, month, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            foreach ($categories as $category) {
                if (!isset($category['name']) || !isset($category['amount']) || !isset($category['is_percentage'])) {
                    throw new Exception("Dati categoria non validi");
                }
                
                $categoryName = trim($category['name']);
                $categoryAmount = (float)$category['amount'];
                $categoryIsPercentage = (int)$category['is_percentage'];
                
                if (empty($categoryName)) {
                    throw new Exception("Nome categoria non può essere vuoto");
                }
                
                $stmt->bind_param("isdis", 
                    $userId,
                    $categoryName,
                    $categoryAmount,
                    $categoryIsPercentage,
                    $monthFormatted
                );
                $stmt->execute();
            }
        }

        $conn->commit();
        
        echo json_encode([
            "success" => true,
            "message" => "Preferenze salvate con successo"
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
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
        echo json_encode(["success" => false, "message" => "ID categoria mancante o non valido"]);
        exit;
    }

    if (!isset($input['name']) || !isset($input['amount']) || !isset($input['is_percentage'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Dati categoria mancanti"]);
        exit;
    }

    $userId = (int)$input['user_id'];
    $categoryId = (int)$input['id'];
    $categoryName = trim($input['name']);
    $categoryAmount = (float)$input['amount'];
    $categoryIsPercentage = (int)$input['is_percentage'];

    if (empty($categoryName)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Nome categoria non può essere vuoto"]);
        exit;
    }

    // Verifica che la categoria appartenga all'utente
    $checkStmt = $conn->prepare("SELECT id FROM budget_categories WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $categoryId, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Categoria non trovata o non autorizzata"]);
        exit;
    }

    // Aggiorna la categoria
    $stmt = $conn->prepare("UPDATE budget_categories SET name = ?, amount = ?, is_percentage = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sdiii", 
        $categoryName,
        $categoryAmount,
        $categoryIsPercentage,
        $categoryId,
        $userId
    );

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Categoria aggiornata con successo"
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Nessuna categoria trovata per l'aggiornamento"]);
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
    if (!isset($input['user_id']) || !is_numeric($input['user_id'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID utente mancante o non valido"]);
        exit;
    }
    
    if (!isset($input['id']) || !is_numeric($input['id'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID categoria mancante o non valido"]);
        exit;
    }

    $userId = (int)$input['user_id'];
    $categoryId = (int)$input['id'];

    // Verifica che la categoria appartenga all'utente
    $checkStmt = $conn->prepare("SELECT id FROM budget_categories WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $categoryId, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Categoria non trovata o non autorizzata"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM budget_categories WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $categoryId, $userId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Categoria eliminata con successo"
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Nessuna categoria trovata per l'eliminazione"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Errore nell'eliminazione: " . $conn->error]);
    }
}
?>