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
$campos_obrigatorios = ['cliente_id', 'valor_solicitado', 'taxa_juros', 'prazo_meses', 'valor_parcela', 'valor_total'];
foreach ($campos_obrigatorios as $campo) {
    if (!isset($data[$campo]) || $data[$campo] === '') {
        echo json_encode(['success' => false, 'message' => "Campo $campo é obrigatório"]);
        exit;
    }
}

// Verificar se cliente existe
$query = "SELECT id, nome, renda_mensal FROM clientes WHERE id = :cliente_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':cliente_id', $data['cliente_id']);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo json_encode(['success' => false, 'message' => 'Cliente não encontrado']);
    exit;
}

$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

// Análise de crédito básica
$aprovado = analisarCredito($cliente, $data);
$status = $aprovado ? 'aprovado' : 'rejeitado';

// Inserir empréstimo
$query = "INSERT INTO emprestimos (cliente_id, valor_solicitado, taxa_juros, prazo_meses, valor_parcela, valor_total, status, observacoes)
          VALUES (:cliente_id, :valor_solicitado, :taxa_juros, :prazo_meses, :valor_parcela, :valor_total, :status, :observacoes)";

$stmt = $db->prepare($query);
$stmt->bindParam(':cliente_id', $data['cliente_id']);
$stmt->bindParam(':valor_solicitado', $data['valor_solicitado']);
$stmt->bindParam(':taxa_juros', $data['taxa_juros']);
$stmt->bindParam(':prazo_meses', $data['prazo_meses']);
$stmt->bindParam(':valor_parcela', $data['valor_parcela']);
$stmt->bindParam(':valor_total', $data['valor_total']);
$stmt->bindParam(':status', $status);

$observacoes = $aprovado ? 'Empréstimo aprovado automaticamente' : 'Empréstimo rejeitado - análise de crédito';
$stmt->bindParam(':observacoes', $observacoes);

if ($stmt->execute()) {
    $emprestimo_id = $db->lastInsertId();
    
    if ($aprovado) {
        // Atualizar data de aprovação
        $query = "UPDATE emprestimos SET data_aprovacao = NOW() WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $emprestimo_id);
        $stmt->execute();
    }
    
    echo json_encode([
        'success' => true, 
        'message' => $aprovado ? 'Empréstimo aprovado!' : 'Empréstimo solicitado - aguardando análise',
        'emprestimo_id' => $emprestimo_id,
        'status' => $status
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao processar solicitação']);
}

function analisarCredito($cliente, $emprestimo) {
    // Critérios de aprovação automática:
    // 1. Parcela não pode ser mais que 30% da renda
    // 2. Valor mínimo de renda: R$ 1.500
    // 3. Valor máximo do empréstimo: R$ 50.000
    
    $renda_mensal = $cliente['renda_mensal'];
    $valor_parcela = $emprestimo['valor_parcela'];
    $valor_solicitado = $emprestimo['valor_solicitado'];
    
    // Renda mínima
    if ($renda_mensal < 1500) {
        return false;
    }
    
    // Valor máximo
    if ($valor_solicitado > 50000) {
        return false;
    }
    
    // Comprometimento de renda (máximo 30%)
    $comprometimento = ($valor_parcela / $renda_mensal) * 100;
    if ($comprometimento > 30) {
        return false;
    }
    
    return true;
}
?> 