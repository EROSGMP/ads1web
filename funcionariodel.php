<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exclusão de Funcionarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="./style.css">
    <?php include('config.php'); ?>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Exclusão de Clientes</h1>
        
        <form action="Funcionariodel.php" method="post" class="mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="id" class="form-label">Matrícula:</label>
                            <input type="number" class="form-control" id="matricula" name="matricula" min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="nome" class="form-label">Nome:</label>
                            <input type="text" class="form-control" id="nome" name="nome">
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger" name="action" value="delete">Excluir Funcionario</button>
                    </div>
                </div>
            </div>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
            // Validação dos dados de entrada
            $matricula = isset($_POST['matricula']) ? intval($_POST['matricula']) : 0;
            $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
            
            if ($matricula > 0 || !empty($nome)) {
                try {
                    // Prevenção contra SQL Injection
                    if ($matricula > 0) {
                        $stmt = $mysqli->prepare("DELETE FROM Funcionario WHERE matricula = ?");
                        $stmt->bind_param("i", $matricula);
                    } else {
                        $stmt = $mysqli->prepare("DELETE FROM Funcionario WHERE nome = ?");
                        $stmt->bind_param("s", $nome);
                    }
                    
                    $stmt->execute();
                    
                    if ($stmt->affected_rows > 0) {
                        echo '<div class="alert alert-success">Funcionario excluído com sucesso!</div>';
                    } else {
                        echo '<div class="alert alert-warning">Nenhum Funcionario encontrado com os critérios fornecidos.</div>';
                    }
                    
                    $stmt->close();
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger">Erro ao excluir Funcionario: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            } else {
                echo '<div class="alert alert-warning">Por favor, informe pelo menos um critério de busca (Matrícula ou Nome).</div>';
            }
        }
        ?>
        
        <div class="text-center mt-3">
            <a href="index.html" class="btn btn-primary">Voltar para Home</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>