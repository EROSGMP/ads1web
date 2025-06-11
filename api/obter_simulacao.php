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

$simulacao_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$simulacao_id) {
    echo json_encode(['success' => false, 'message' => 'ID da simulação é obrigatório']);
    exit;
}

$query = "SELECT * FROM simulacoes WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $simulacao_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo json_encode(['success' => false, 'message' => 'Simulação não encontrada']);
    exit;
}

$simulacao = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'simulacao' => $simulacao
]);
?> 