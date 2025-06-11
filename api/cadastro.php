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
$campos_obrigatorios = ['nome', 'email', 'cpf', 'telefone', 'data_nascimento', 'renda_mensal', 'senha'];
foreach ($campos_obrigatorios as $campo) {
    if (empty($data[$campo])) {
        echo json_encode(['success' => false, 'message' => "Campo $campo é obrigatório"]);
        exit;
    }
}

// Validar CPF
if (!validarCPF($data['cpf'])) {
    echo json_encode(['success' => false, 'message' => 'CPF inválido']);
    exit;
}

// Validar email
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'E-mail inválido']);
    exit;
}

// Verificar se já existe cliente com mesmo email ou CPF
$query = "SELECT id FROM clientes WHERE email = :email OR cpf = :cpf";
$stmt = $db->prepare($query);
$stmt->bindParam(':email', $data['email']);
$stmt->bindParam(':cpf', $data['cpf']);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => false, 'message' => 'E-mail ou CPF já cadastrado']);
    exit;
}

// Hash da senha usando MD5
$senha_hash = md5($data['senha']);

// Inserir cliente
$query = "INSERT INTO clientes (nome, email, cpf, telefone, data_nascimento, renda_mensal, senha, endereco) 
          VALUES (:nome, :email, :cpf, :telefone, :data_nascimento, :renda_mensal, :senha, :endereco)";

$stmt = $db->prepare($query);
$stmt->bindParam(':nome', $data['nome']);
$stmt->bindParam(':email', $data['email']);
$stmt->bindParam(':cpf', $data['cpf']);
$stmt->bindParam(':telefone', $data['telefone']);
$stmt->bindParam(':data_nascimento', $data['data_nascimento']);
$stmt->bindParam(':renda_mensal', $data['renda_mensal']);
$stmt->bindParam(':senha', $senha_hash);
$stmt->bindParam(':endereco', $data['endereco']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cliente cadastrado com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar cliente']);
}

function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);
    
    if (strlen($cpf) != 11) {
        return false;
    }
    
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}
?> 