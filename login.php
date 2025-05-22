<?php
include ('dbConfig.php'); 
session_start();

// Session timeout: 20 minutes (1200 seconds)
$timeout_duration = 1200;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - NewBank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-box">
        <h2>Login no NewBank</h2>
        <form method="post" action="login.php">
            <input type="text" name="nome" placeholder="Usuário" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit" name="botao" value="Entrar">Entrar</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['botao']) && $_POST['botao'] == "Entrar") {
            $nome = $_POST['nome'];
            $senha = $_POST['senha'];

            $stmt = $conn->prepare("SELECT senha, cpf FROM usuario WHERE nome = ?");
            $stmt->bind_param("s", $nome);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($stored_password, $cpf);
                $stmt->fetch();

                if ($senha === $stored_password) {
                    $_SESSION['usuario'] = $nome;
                    $_SESSION['cpf'] = $cpf; 
                    $_SESSION['senha'] = $senha;
                    $_SESSION['LAST_ACTIVITY'] = time(); // update activity time on login
                    header("Location: painel.php");
                    exit();
                } else {
                    echo '<p class="error-message">Senha inválida!</p>';
                }
            } else {
                echo '<p class="error-message">Usuário não encontrado!</p>';
            }

            $stmt->close();
        }
        ?>

        <?php
        if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
            echo '<p class="error-message">Sessão expirada por inatividade. Faça login novamente.</p>';
        }
        ?>

        <a href="register.php" class="register-link">Registrar</a>
        <a href="index.php" class="back-link">← Voltar à Página Inicial</a>
    </div>
</body>
</html>
