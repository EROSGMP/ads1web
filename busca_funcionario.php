<?php
include('config.php');

header('Content-Type: application/json');

if(isset($_GET['matricula'])) {
    $matricula = intval($_GET['matricula']);
    
    if($matricula > 0) {
        $stmt = $mysqli->prepare("SELECT nome, cpf, data_ncto FROM Funcionario WHERE matricula = ?");
        $stmt->bind_param("i", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            echo json_encode(['error' => 'Funcionario não encontrado']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Matrícula inválida']);
    }
} else {
    echo json_encode(['error' => 'Matrícula não informada']);
}

$mysqli->close();
?>