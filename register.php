<?php include ('dbConfig.php')?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuário</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function validatePasswords(event) {
            const senha = document.querySelector('input[name="senha"]');
            const confirmarSenha = document.querySelector('input[name="confirmar_senha"]');
            const errorMessage = document.getElementById('error-message');

            if (senha.value !== confirmarSenha.value) {
                event.preventDefault();
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }
        }

        function validateForm(event) {
            validatePasswords(event);
            validateEmail(event);
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Registro de Usuário</h2>
        <form action="register.php" method="POST" onsubmit="validateForm(event)">
            <input type="text" name="nome" placeholder="Nome completo" required>
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="password" name="confirmar_senha" placeholder="Confirme sua senha" required>
            <p id="error-message" class="error-message">As senhas não coincidem.</p>
            <input type="text" name="cpf" placeholder="12345678910" required>
            <button type="submit" name="botao" value="Registrar">Registrar</button>
        </form>
        
        <a class="back-link" href="login.php">Já tenho uma conta</a>
        <a class="back-link" href="index.php">← Voltar à Página Inicial</a>
    </div>
</body>
</html>

<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['botao']) && $_POST['botao'] == "Registrar") {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $cpf = $_POST['cpf'];

        
        $insertQuery = "INSERT INTO usuario (nome, email, senha, cpf) VALUES ('$nome', '$email', '$senha', '$cpf')";

        $checkQuery = "SELECT * FROM usuario WHERE nome = '$nome'";
        $result = $conn->query($checkQuery);

        if ($result->num_rows > 0) {
            echo "<script>alert('O nome já está registrado. Por favor, escolha outro.');</script>";
        } else {
            // Insert the new user if 'nome' does not exist
            
            if ($conn->query($insertQuery) === TRUE) {
                echo "<script>alert('Registro realizado com sucesso!'); window.location.href = 'login.php';</script>";
            } else {
                echo "Erro ao registrar: " . $conn->error;
            }
        }

    }

?>

