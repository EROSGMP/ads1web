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
$campos_obrigatorios = ['valor', 'prazo_meses', 'taxa_juros', 'valor_parcela', 'valor_total'];
foreach ($campos_obrigatorios as $campo) {
    if (!isset($data[$campo]) || $data[$campo] === '') {
        echo json_encode(['success' => false, 'message' => "Campo $campo é obrigatório"]);
        exit;
    }
}

// cliente_id pode ser null para simulações anônimas
$cliente_id = isset($data['cliente_id']) ? $data['cliente_id'] : null;

// Inserir simulação
$query = "INSERT INTO simulacoes (cliente_id, valor, prazo_meses, taxa_juros, valor_parcela, valor_total)
          VALUES (:cliente_id, :valor, :prazo_meses, :taxa_juros, :valor_parcela, :valor_total)";

$stmt = $db->prepare($query);
$stmt->bindParam(':cliente_id', $cliente_id);
$stmt->bindParam(':valor', $data['valor']);
$stmt->bindParam(':prazo_meses', $data['prazo_meses']);
$stmt->bindParam(':taxa_juros', $data['taxa_juros']);
$stmt->bindParam(':valor_parcela', $data['valor_parcela']);
$stmt->bindParam(':valor_total', $data['valor_total']);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Simulação salva com sucesso',
        'simulacao_id' => $db->lastInsertId()
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar simulação']);
}
?> 