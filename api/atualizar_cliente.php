<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);

// Validar dados obrigatórios
if (empty($data['id']) || empty($data['nome']) || empty($data['telefone']) || empty($data['renda_mensal'])) {
    echo json_encode(['success' => false, 'message' => 'Dados obrigatórios não fornecidos']);
    exit;
}

// Verificar se cliente existe
$query = "SELECT id FROM clientes WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $data['id']);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo json_encode(['success' => false, 'message' => 'Cliente não encontrado']);
    exit;
}

// Atualizar dados
$query = "UPDATE clientes SET nome = :nome, telefone = :telefone, renda_mensal = :renda_mensal, endereco = :endereco, updated_at = NOW() WHERE id = :id";

$stmt = $db->prepare($query);
$stmt->bindParam(':id', $data['id']);
$stmt->bindParam(':nome', $data['nome']);
$stmt->bindParam(':telefone', $data['telefone']);
$stmt->bindParam(':renda_mensal', $data['renda_mensal']);
$stmt->bindParam(':endereco', $data['endereco']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Dados atualizados com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar dados']);
}
?> 