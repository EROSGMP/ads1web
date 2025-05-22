<?php
include ('dbConfig.php');
session_start(); 
$mensagem = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valor = $_POST["valor"];
    $prazo = $_POST["prazo"];
    $taxa = 5.50; 
    $dtaInicio = date('Y-m-d');

    if (isset($_SESSION['cpf'])) {
        $cpf = $_SESSION['cpf'];
        $$taxa = (1  + ($taxa / 100)); // Cálculo da taxa de juros
        $total = $valor + ($valor * $prazo / 12); // Cálculo do total com juros
        
        $insertQuery = "INSERT INTO emprestimo (total, taxa, CPF, dtaInicio) VALUES ('$total', '$taxa', '$cpf', '$dtaInicio')";

        if ($conn->query($insertQuery) === TRUE) {
            $mensagem = "Empréstimo de R$ " . number_format($valor, 2, ',', '.') . " solicitado para pagar em $prazo meses!";
        } else {
            $mensagem = "Erro ao solicitar empréstimo: " . $conn->error;
        }
    } else {
        $mensagem = "Erro: CPF do usuário não encontrado na sessão.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Empréstimo - NewBank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>NewBank</h1>
        <p>Solicite seu empréstimo de forma rápida e segura</p>
    </header>

    <main>
        <div class="form-box">
            <h2>Simulação de Empréstimo</h2>
            <form method="post" action="">
                <label for="valor">Valor desejado:</label>
                <input type="number" name="valor" id="valor" required min="100" step="100" placeholder="Ex: 5000">

                <label for="prazo">Prazo para pagamento:</label>
                <select name="prazo" id="prazo" required>
                    <option value="6">6 meses</option>
                    <option value="12">12 meses</option>
                    <option value="18">18 meses</option>
                    <option value="24">24 meses</option>
                    <option value="36">36 meses</option>
                </select>

                <button type="submit">Solicitar</button>
            </form>

            <?php if (!empty($mensagem)): ?>
                <div class="mensagem"><?= $mensagem ?></div>
            <?php endif; ?>

            <a href="painel.php" class="voltar">← Voltar ao painel</a>
        </div>
    </main>

    <footer>
        &copy; <?php echo date('Y'); ?> NewBank. Todos os direitos reservados.
    </footer>

</body>
</html>
