<?php
// ✅ Intestazioni CORS per Flutter Web
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// ✅ Gestione del preflight CORS (necessaria per richieste da browser)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ✅ Tipo di contenuto JSON
header("Content-Type: application/json; charset=UTF-8");

// ✅ Connessione al DB
$host = "localhost";
$username = "root";
$password = "root"; // se XAMPP non ha password, lascia ''
$database = "easybudget";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode([
        "success" => false,
        "message" => "Connessione fallita: " . $conn->connect_error
    ]));
}
?>
