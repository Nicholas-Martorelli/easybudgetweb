<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

include 'config.php';

$data = json_decode(file_get_contents("php://input"));
$email = trim($data->email ?? '');
$password = trim($data->password ?? '');

if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Email e password sono obbligatori."]);
    exit;
}

$sql = "SELECT id, password FROM utenti WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Email non trovata."]);
    exit;
}

$user = $result->fetch_assoc();

if (password_verify($password, $user['password'])) {
    // Login riuscito
    echo json_encode([
        "success" => true,
        "message" => "Login effettuato con successo",
        "user_id" => $user['id']
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Password errata."]);
}

$stmt->close();
$conn->close();
?>