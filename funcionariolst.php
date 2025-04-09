<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Funcionarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <div class="container">
        <h1>Relatório de Funcionarios</h1>
        <form action="Funcionariolst.php" method="post" name="form1">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" placeholder="Digite o nome do Funcionario">
            <input type="submit" name="botao" value="Gerar">
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['botao'])) {
            include('config.php');

            // Sanitização dos dados
            $nome = mysqli_real_escape_string($mysqli, htmlspecialchars($_POST['nome']));

            // Consulta ao banco de dados
            $query = "SELECT * FROM Funcionario WHERE matricula > 0 ";
            $query .= ($nome ? " AND nome LIKE '%$nome%' " : "");
            $query .= " ORDER BY matricula";
            $result = mysqli_query($mysqli, $query);

            if (mysqli_num_rows($result) > 0) {
                echo '<table>
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Data de Nascimento</th>
                            </tr>
                        </thead>
                        <tbody>';

                while ($coluna = mysqli_fetch_array($result)) {
                    echo '<tr>
                            <td>' . htmlspecialchars($coluna['matricula']) . '</td>
                            <td>' . htmlspecialchars($coluna['nome']) . '</td>
                            <td>' . htmlspecialchars($coluna['cpf']) . '</td>
                            <td>' . htmlspecialchars($coluna['data_ncto']) . '</td>
                          </tr>';
                }

                echo '</tbody></table>';
            } else {
                echo '<p>Nenhum Funcionario encontrado.</p>';
            }
        }
        ?>

        <a href="index.html" class="home-link">Home</a>
    </div>
</body>
</html>