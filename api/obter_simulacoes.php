<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$cliente_id = isset($_GET['cliente_id']) ? $_GET['cliente_id'] : null;

if (!$cliente_id) {
    echo json_encode(['success' => false, 'message' => 'ID do cliente é obrigatório']);
    exit;
}

$query = "SELECT * FROM simulacoes WHERE cliente_id = :cliente_id ORDER BY created_at DESC LIMIT 10";
$stmt = $db->prepare($query);
$stmt->bindParam(':cliente_id', $cliente_id);
$stmt->execute();

$simulacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'simulacoes' => $simulacoes
]);
?> 