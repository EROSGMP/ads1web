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

if (empty($data['email']) || empty($data['senha'])) {
    echo json_encode(['success' => false, 'message' => 'E-mail e senha são obrigatórios']);
    exit;
}

$query = "SELECT id, nome, email, cpf, telefone, data_nascimento, renda_mensal, endereco, senha 
          FROM clientes WHERE email = :email";
$stmt = $db->prepare($query);
$stmt->bindParam(':email', $data['email']);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo json_encode(['success' => false, 'message' => 'E-mail não encontrado']);
    exit;
}

$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar senha usando MD5
$senha_md5 = md5($data['senha']);
if ($senha_md5 !== $cliente['senha']) {
    echo json_encode(['success' => false, 'message' => 'Senha incorreta']);
    exit;
}

// Remover senha do retorno
unset($cliente['senha']);

echo json_encode([
    'success' => true, 
    'message' => 'Login realizado com sucesso',
    'user' => $cliente
]);
?> 