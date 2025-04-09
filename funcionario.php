<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <div class="container">
        <h1>Cadastro de Funcionarios</h1>
        <form action="Funcionario.php" c>
            <label for="matricula">Matrícula:</label>
            <input type="text" id="matricula" name="matricula" readonly>

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" required>

            <label for="data_ncto">Data de Nascimento:</label>
            <input type="date" id="data_ncto" name="data_ncto" required>

            <input type="submit" value="Gravar" name="botao">
        </form>
        <a href="index.html" class="home-link">Home</a>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['botao'])) {
        include('config.php');

        // Sanitização dos dados
        $nome = mysqli_real_escape_string($mysqli, htmlspecialchars($_POST['nome']));
        $cpf = mysqli_real_escape_string($mysqli, htmlspecialchars($_POST['cpf']));
        $data_ncto = mysqli_real_escape_string($mysqli, htmlspecialchars($_POST['data_ncto']));

        // Inserção no banco de dados
        $insere = "INSERT INTO Funcionario (nome, cpf, data_ncto) VALUES ('$nome', '$cpf', '$data_ncto')";
        if (mysqli_query($mysqli, $insere)) {
            echo "<script>alert('Dados gravados com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao gravar dados: " . mysqli_error($mysqli) . "');</script>";
        }
    }
    ?>
</body>
</html>