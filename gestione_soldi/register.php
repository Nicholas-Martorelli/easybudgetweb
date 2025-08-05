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

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO utenti (email, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Email già registrata."]);
}

$stmt->close();
$conn->close();
?>
